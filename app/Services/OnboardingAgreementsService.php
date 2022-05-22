<?php

namespace App\Services;

use App\Http\Requests\OnboardingAgreements\CreateTmpRequest;
use App\Http\Requests\OnboardingAgreements\DeclineRequest;
use App\Http\Requests\OnboardingAgreements\UpdateRequest;
use App\Libraries\RRFpdi;
use App\Libraries\WSProvider;
use App\Models\AgreementFiles;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Offer;
use App\Models\OnboardingAgreements;
use App\Models\PersonalInfoRequest;
use App\Models\Room;
use App\Models\RoomParam;
use App\Models\SearcherProfile;
use App\Models\SuperUserProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use setasign\FpdiPdfParser\PdfParser\PdfParser;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OnboardingAgreementsService extends Service
{
    public function createTmp(CreateTmpRequest $request): OnboardingAgreements
    {
        /** @var User $su */
        $su = User::find(auth()->id());
        /** @var User $su */
        $searcher = User::find($request->searcher_id);
        /** @var SuperUserProfile $su */
        $suProfile = SuperUserProfile::find(auth()->id());
        /** @var SearcherProfile $su */
        $searcherProfile = SearcherProfile::find($request->searcher_id);

        /** @var Offer $offer */
        $offer = Offer::where([
                'chat_id' => $request->chat_id,
                'status' => Offer::STATUS_ACCEPTED,
            ])
            ->orderBy('created_at', 'desc')
            ->first();

        /** @var Room $room */
        $room = Room::find($offer->room_id);
        /** @var RoomParam $roomRentalPeriod */
        $roomRentalPeriod = RoomParam::where([
            'room_id' => $room->id,
            'param_id' => RoomParam::RENT_ID,
        ])->first();

        /** @var PersonalInfoRequest $personalInfo */
        $personalInfo = PersonalInfoRequest::where([
                'chat_id' => $request->chat_id,
                'status' => PersonalInfoRequest::STATUS_ACCEPTED,
            ])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$su) {
            throw new InvalidParameterException('Superuser not found.');
        }
        if (!$suProfile) {
            throw new InvalidParameterException('Superuser profile not found.');
        }
        if (!$searcher) {
            throw new InvalidParameterException('Searcher not found.');
        }
        if (!$searcherProfile) {
            throw new InvalidParameterException('Searcher profile not found.');
        }
        if (!$offer) {
            throw new InvalidParameterException('Offer in this onboarding process not found.');
        }
        if (!$personalInfo) {
            throw new InvalidParameterException('Personal info in this onboarding process not found.');
        }
        if (!$room) {
            throw new InvalidParameterException('Room not found.');
        }

        $agreement = new OnboardingAgreements();
        $agreement->creator_id = auth()->id();
        $agreement->searcher_id = $request->searcher_id;
        $agreement->chat_id = $request->chat_id;

        $agreement->su_details = json_encode([
            'firstName' => $su->first_name,
            'lastName' => $su->last_name,
            'phoneNumber' => $suProfile->phone ?: $su->phone_number,
            'email' => $su->email,
            'companyAddress' => $suProfile->company_address,
        ]);

        $agreement->searcher_details = json_encode([
            'firstName' => $searcher->first_name,
            'lastName' => $searcher->last_name,
            'phoneNumber' => $searcher->phone_number,
            'email' => $searcher->email,
            'birthday' => $personalInfo->birthday,
            'address' => $personalInfo->address,
        ]);

        $agreement->rent = $offer->rent_amount;
        $agreement->rent_period = $roomRentalPeriod->param_value;
        $agreement->bond = $offer->bond;
        $agreement->bills_amount = $offer->bills_amount;
        $agreement->bills_included = $offer->bills_included;

        $agreement->save();

        return $agreement;
    }

    public function update(OnboardingAgreements $agreements, UpdateRequest $request): OnboardingAgreements
    {
        if ($agreements->status !== OnboardingAgreements::STATUS_SU_SIGNED) {
            throw new InvalidParameterException('Agreements can not be signed.');
        }

        $agreements->fill($request->toArray());
        $agreements->status = OnboardingAgreements::STATUS_SU_SIGNED_AND_UPDATED;
        $agreements->save();

        WSProvider::sendSpecialMessage(
            $agreements->chat_id,
            $agreements->creator_id,
            Message::TYPE_ONBOARDING_AGREEMENT_SU_REQUEST_SEND,
            ['id' => $agreements->id]
        );

        /** @var AgreementFiles $agreementFile */
        $agreementFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_AGREEMENT_SU_SIGNED)->first();
        /** @var AgreementFiles $houseRulesFile */
        $houseRulesFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED)->first();

        $agreementsData = [
            'id' => $agreements->id,
            'signedDocs' => [
                'agreement' => [[
                    'id' => $agreementFile->id,
                    'url' => $agreementFile->getFileNameAttribute(),
                ]],
                'houseRules' => [[
                    'id' => $houseRulesFile->id,
                    'url' => $houseRulesFile->getFileNameAttribute(),
                ]],
            ],
        ];

        WSProvider::changeChatState(
            $agreements->chat_id,
            ['agreement' => $agreementsData]
        );

        return $agreements;
    }

    public function decline(OnboardingAgreements $agreements, DeclineRequest $request): OnboardingAgreements
    {
        if (!in_array($agreements->status, [
            OnboardingAgreements::STATUS_SEND,
            OnboardingAgreements::STATUS_SU_SIGNED,
            OnboardingAgreements::STATUS_SU_SIGNED_AND_UPDATED,
        ])) {
            throw new InvalidParameterException('Agreement cannot be declined.');
        }

        $agreements->status = OnboardingAgreements::STATUS_DECLINED;
        $agreements->save();

        $message = Message::find($request->messageId);
        $message->type = Message::TYPE_ONBOARDING_AGREEMENT_SU_REQUEST_HIDE;
        $message->save();

        WSProvider::sendSpecialMessage(
           $message->chat_id,
           $agreements->searcher_id,
           Message::TYPE_ONBOARDING_AGREEMENT_RS_DECLINED,
           ['id' => $agreements->id]
        );

        return $agreements;
    }

    public function uploadAgreement(OnboardingAgreements $agreements): AgreementFiles
    {
        try {
            DB::beginTransaction();

            $file = Input::file('file');
            $pInfo = pathinfo($file->getClientOriginalName());

            $storageType = env('FILES_STORAGE_DRIVER');

            AgreementFiles::where([
                'scope_id' => auth()->id(),
                'type' => AgreementFiles::TYPE_AGREEMENT_ORIGIN,
            ])->delete();

            $agreementFile = new AgreementFiles();
            $agreementFile->creator_id = auth()->id();
            $agreementFile->parent_entity_id = $agreements->id;
            $agreementFile->scope_id = auth()->id();
            $agreementFile->name = $pInfo['basename'];
            $agreementFile->type = AgreementFiles::TYPE_AGREEMENT_ORIGIN;
            $agreementFile->save();

            request()->file('file')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $agreementFile;
    }

    public function uploadHouseRules(OnboardingAgreements $agreements): AgreementFiles
    {
        try {
            DB::beginTransaction();

            $file = Input::file('file');
            $pInfo = pathinfo($file->getClientOriginalName());

            $storageType = env('FILES_STORAGE_DRIVER');

            $chat = Chat::find($agreements->chat_id);
            $room = Room::find($chat->room_id);

            AgreementFiles::where([
                'scope_id' => $room->property_id,
                'type' => AgreementFiles::TYPE_HOUSE_RULES_ORIGIN,
            ])->delete();

            $agreementFile = new AgreementFiles();
            $agreementFile->creator_id = auth()->id();
            $agreementFile->parent_entity_id = $agreements->id;
            $agreementFile->scope_id = $room->property_id;
            $agreementFile->name = $pInfo['basename'];
            $agreementFile->type = AgreementFiles::TYPE_HOUSE_RULES_ORIGIN;
            $agreementFile->save();

            request()->file('file')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $agreementFile;
    }

    public function signDocuments(OnboardingAgreements $agreements)
    {
        $file = Input::file('agreement');
        $pInfo = pathinfo($file->getClientOriginalName());

        if ($pInfo['basename'] === 'blob') {
            $pInfo['basename'] = 'agreement.pdf';
        }

        $storageType = env('FILES_STORAGE_DRIVER');

        $agreementFile = new AgreementFiles();
        $agreementFile->creator_id = auth()->id();
        $agreementFile->parent_entity_id = $agreements->id;
        $agreementFile->scope_id = $agreements->id;
        $agreementFile->name = $pInfo['basename'];
        $agreementFile->type = AgreementFiles::TYPE_AGREEMENT_SU_SIGNED;
        $agreementFile->save();

        request()->file('agreement')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        //https://tcpdf.org/examples/example_052/
        $pdf = new RRFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setPdfParserClass(PdfParser::class);

        $path = base_path('storage/app/public/' . $agreementFile->getFileNameAttribute());

        $pages = $pdf->setSourceFile($path);

        $info = [
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Room Host signed',
            'ContactInfo' => 'http://www.tcpdf.org',
        ];

        $certFilename = 'file://' . base_path('tcpdf.crt');

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);
            $pdf->setSignature($certFilename, $certFilename, '', '', 2, $info);
        }

//        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
//        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
//            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '__sign.' . $pathInfo['extension']);
//        $pathInfo = pathinfo($newPath);

        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
        $pathInfo['extension'] = $pathInfo['extension'] ?? 'pdf';

        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $pathInfo['extension']);
        $pathInfo = pathinfo($newPath);

        $pdf->Output($newPath, 'F');

        $file = Input::file('houseRules');
        $pInfo = pathinfo($file->getClientOriginalName());

        if ($pInfo['basename'] === 'blob') {
            $pInfo['basename'] = 'houseRules.pdf';
        }

        $storageType = env('FILES_STORAGE_DRIVER');

        $agreementFile = new AgreementFiles();
        $agreementFile->creator_id = auth()->id();
        $agreementFile->parent_entity_id = $agreements->id;
        $agreementFile->scope_id = $agreements->id;
        $agreementFile->name = $pInfo['basename'];
        $agreementFile->type = AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED;
        $agreementFile->save();

        request()->file('houseRules')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        //https://tcpdf.org/examples/example_052/
        $pdf = new RRFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setPdfParserClass(PdfParser::class);

        $path = base_path('storage/app/public/' . $agreementFile->getFileNameAttribute());

        $pages = $pdf->setSourceFile($path);

        $info = [
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Room Host signed',
            'ContactInfo' => 'http://www.tcpdf.org',
        ];

        $certFilename = 'file://' . base_path('tcpdf.crt');

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);
            $pdf->setSignature($certFilename, $certFilename, '', '', 2, $info);
        }

//        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
//        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
//            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '__sign.' . $pathInfo['extension']);
//        $pathInfo = pathinfo($newPath);

        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
        $pathInfo['extension'] = $pathInfo['extension'] ?? 'pdf';

        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $pathInfo['extension']);
        $pathInfo = pathinfo($newPath);

        $pdf->Output($newPath, 'F');

        $agreements->status = OnboardingAgreements::STATUS_SU_SIGNED;
        $agreements->save();

        $agreementsDocs = AgreementFiles::where([
                'parent_entity_id' => $agreements->id,
            ])
            ->whereIn('type', [
                AgreementFiles::TYPE_AGREEMENT_SU_SIGNED,
                AgreementFiles::TYPE_AGREEMENT_RS_SIGNED,
                AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED,
                AgreementFiles::TYPE_HOUSE_RULES_RS_SIGNED,
            ])
            ->get()
            ->all();

        $docs = [];
        /** @var AgreementFiles $doc */
        foreach ($agreementsDocs as $doc) {
            $docs[] = [
                'id' => $doc->id,
                'name' => $doc->name,
                'url' => 'files/agreement-files/' . $doc->id . '/' . $doc->name,
                'updated' => $doc->updated_at ? $doc->updated_at->format('Y-m-d H:i:s') : '',
                'moveData' => $agreements->move_date ? $agreements->move_date->format('Y-m-d H:i:s') : '',
                'type' => in_array($doc->type, [AgreementFiles::TYPE_AGREEMENT_SU_SIGNED, AgreementFiles::TYPE_AGREEMENT_RS_SIGNED]) ?
                    'Agreement' : 'House Rules',
                'status' => in_array($doc->type, [AgreementFiles::TYPE_AGREEMENT_SU_SIGNED, AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED]) ?
                    'Waiting for sign' : 'Signed',
            ];
        }

        WSProvider::changeChatState(
            $agreements->chat_id,
            [
                'agreementDocs' => $docs,
                'onboarding_step' => Chat::ONBOARDING_STEP_AGREEMENT_REQUESTED,
            ]
        );
    }

    public function signSearcherDocuments(OnboardingAgreements $agreements)
    {
        $file = Input::file('agreement');
        $pInfo = pathinfo($file->getClientOriginalName());

        if ($pInfo['basename'] === 'blob') {
            $pInfo['basename'] = 'agreement.pdf';
        }

        $storageType = env('FILES_STORAGE_DRIVER');

        /** @var AgreementFiles $agreementFile */
        $agreementFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_AGREEMENT_SU_SIGNED)->first();
        if (!$agreementFile) {
            $agreementFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_AGREEMENT_RS_SIGNED)->first();
        }

        $agreementFile->type = AgreementFiles::TYPE_AGREEMENT_RS_SIGNED;
        $agreementFile->save();

        request()->file('agreement')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        $pdf = new RRFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setPdfParserClass(PdfParser::class);

        $path = base_path('storage/app/public/' . $agreementFile->getFileNameAttribute());

        $pages = $pdf->setSourceFile($path);

        $info = [
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Room Host & Room Renter signed',
            'ContactInfo' => 'http://www.tcpdf.org',
        ];

        $certFilename = 'file://' . base_path('tcpdf.crt');

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);
            $pdf->setSignature($certFilename, $certFilename, '', '', 2, $info);
        }

        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
        $pathInfo['extension'] = $pathInfo['extension'] ?? 'pdf';

        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $pathInfo['extension']);

        $pdf->Output($newPath, 'F');

        $file = Input::file('houseRules');
        $pInfo = pathinfo($file->getClientOriginalName());

        if ($pInfo['basename'] === 'blob') {
            $pInfo['basename'] = 'houseRules.pdf';
        }

        $storageType = env('FILES_STORAGE_DRIVER');

        /** @var AgreementFiles $agreementFile */
        $agreementFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED)->first();
        if (!$agreementFile) {
            $agreementFile = $agreements->getAgreementFiles(AgreementFiles::TYPE_HOUSE_RULES_RS_SIGNED)->first();
        }
        $agreementFile->type = AgreementFiles::TYPE_HOUSE_RULES_RS_SIGNED;
        $agreementFile->save();

        request()->file('houseRules')->storeAs($agreementFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        $pdf = new RRFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setPdfParserClass(PdfParser::class);

        $path = base_path('storage/app/public/' . $agreementFile->getFileNameAttribute());

        $pages = $pdf->setSourceFile($path);

        $info = [
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Room Host & Room Renter signed',
            'ContactInfo' => 'http://www.tcpdf.org',
        ];

        $certFilename = 'file://' . base_path('tcpdf.crt');

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);
            $pdf->setSignature($certFilename, $certFilename, '', '', 2, $info);
        }

        $pathInfo = pathinfo($agreementFile->getFileNameAttribute());
        $pathInfo['extension'] = $pathInfo['extension'] ?? 'pdf';

        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $pathInfo['extension']);

        $pdf->Output($newPath, 'F');

        $agreements->status = OnboardingAgreements::STATUS_RR_SIGNED;
        $agreements->save();

        WSProvider::changeChatState(
            $agreements->chat_id,
            ['onboarding_step' => Chat::ONBOARDING_STEP_AGREEMENT_ACCEPTED]
        );

        $messageId = request()->post('messageId');
        $message = Message::find($messageId);
        if (!$message) {
            $message->type = Message::TYPE_ONBOARDING_AGREEMENT_SU_REQUEST_HIDE;
            $message->save();
        }

        WSProvider::sendSpecialMessage(
            $agreements->chat_id,
            $agreements->searcher_id,
            Message::TYPE_ONBOARDING_AGREEMENT_RS_SINGED,
            ['id' => $agreements->id]
        );

        $agreementsDocs = AgreementFiles::where([
            'parent_entity_id' => $agreements->id,
        ])
            ->whereIn('type', [
                AgreementFiles::TYPE_AGREEMENT_SU_SIGNED,
                AgreementFiles::TYPE_AGREEMENT_RS_SIGNED,
                AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED,
                AgreementFiles::TYPE_HOUSE_RULES_RS_SIGNED,
            ])
            ->get()
            ->all();

        $docs = [];
        /** @var AgreementFiles $doc */
        foreach ($agreementsDocs as $doc) {
            $docs[] = [
                'id' => $doc->id,
                'name' => $doc->name,
                'url' => 'files/agreement-files/' . $doc->id . '/' . $doc->name,
                'updated' => $doc->updated_at ? $doc->updated_at->format('Y-m-d H:i:s') : '',
                'moveData' => $agreements->move_date,
                'type' => in_array($doc->type, [AgreementFiles::TYPE_AGREEMENT_SU_SIGNED, AgreementFiles::TYPE_AGREEMENT_RS_SIGNED]) ?
                    'Agreement' : 'House Rules',
                'status' => in_array($doc->type, [AgreementFiles::TYPE_AGREEMENT_SU_SIGNED, AgreementFiles::TYPE_HOUSE_RULES_SU_SIGNED]) ?
                    'Waiting for sign' : 'Signed',
            ];
        }

        WSProvider::changeChatState(
            $agreements->chat_id,
            ['agreementDocs' => $docs]
        );
    }
}
