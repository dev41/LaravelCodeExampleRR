<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Helpers\ArrayHelper;
use App\Http\Requests\PaymentRequest\CreateTmpRequest;
use App\Http\Requests\PaymentRequest\RSUpdateRequest;
use App\Http\Requests\PaymentRequest\SUUpdateRequest;
use App\Models\Message;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestFiles;
use App\Models\PaymentRequestPrice;
use App\Models\PaymentRequestRsProfile;
use App\Models\SearcherProfile;
use App\Models\SuperUserProfile;
use App\Models\User;
use App\Repositories\PaymentRequestRepository;
use App\Repositories\SearcherMediaFileRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PaymentRequestService extends Service
{
    public function createTmpRequest(CreateTmpRequest $request): PaymentRequest
    {
        $pRequest = new PaymentRequest();
        $pRequest->created_by = auth()->id();
        $pRequest->room_id = $request->roomId;
        $pRequest->amount = 0;
        $pRequest->save();

        return $pRequest;
    }

    public function viewForRs(PaymentRequest $paymentRequest)
    {
        $data = PaymentRequestRepository::getForRs($paymentRequest);
        $data->stripePublicKey = env('STRIPE_PUBLIC_KEY');

        return $data;
    }

    public function getAvailableUserList(int $roomId): array
    {
        $users = PaymentRequestRepository::getAvailableUserList($roomId);

        foreach ($users as $user) {
            if (!$user->avatar) {
                continue;
            }

            $user->avatar = '/' . SearcherMediaFileRepository::getImageFilePath($user->id, $user->avatar);
        }

        return $users;
    }

    public function superuserUpdate(PaymentRequest $paymentRequest, SUUpdateRequest $request): PaymentRequest
    {
        try {
            DB::beginTransaction();

            $paymentRequest->fill($request->toArray());
            $paymentRequest->status = PaymentRequest::STATUS_SENT;

            $rsUsers = User::where(['id' => $request->toUserIds])->get()->toArray();
            $rsUsers = ArrayHelper::setKeysFromMatrixValues($rsUsers, 'id');

            foreach ($request->toUserIds as $userId) {
                $paymentRequestRsProfile = new PaymentRequestRsProfile();
                $paymentRequestRsProfile->payment_request_id = $paymentRequest->id;
                $paymentRequestRsProfile->rs_profile_id = $userId;

                $paymentRequestRsProfile->first_name = $rsUsers[$userId]['first_name'];
                $paymentRequestRsProfile->last_name = $rsUsers[$userId]['last_name'];

                $paymentRequestRsProfile->save();
            }

            $paymentRequestPrice = new PaymentRequestPrice();
            $paymentRequestPrice->payment_request_id = $paymentRequest->id;

            switch ($paymentRequest->purpose) {

                case PaymentRequest::TYPE_DEPOSIT:
                    $paymentRequestPrice->type = PaymentRequest::TYPE_DEPOSIT;
                    $paymentRequestPrice->price = $request->deposit;
                    $paymentRequest->amount = $request->deposit;
                    break;

                case PaymentRequest::TYPE_INITIAL_RENT:
                    $paymentRequestPrice->type = PaymentRequest::TYPE_INITIAL_RENT;
                    $paymentRequestPrice->price = $request->rent;
                    $paymentRequest->amount = $request->rent;
                    break;

                case PaymentRequest::TYPE_BOND:
                    $paymentRequestPrice->type = PaymentRequest::TYPE_BOND;
                    $paymentRequestPrice->price = $request->bond;
                    $paymentRequest->amount = $request->bond;
                    break;

                case PaymentRequest::TYPE_INITIAL_RENT_BOND:
                    $paymentRequestPrice->type = PaymentRequest::TYPE_BOND;
                    $paymentRequestPrice->price = $request->bond;

                    $rentPRPrice = new PaymentRequestPrice();
                    $rentPRPrice->price = $request->rent;
                    $rentPRPrice->type = PaymentRequest::TYPE_INITIAL_RENT;
                    $rentPRPrice->payment_request_id = $paymentRequest->id;
                    $rentPRPrice->save();

                    $paymentRequest->amount = $request->bond + $request->rent;
                    break;

                default: throw new InvalidParameterException('Payment Request type is invalid.');
            }

            $paymentRequest->save();
            $paymentRequestPrice->save();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }

        return $paymentRequest;
    }

    public function searcherUpdate(PaymentRequest $paymentRequest, RSUpdateRequest $request): PaymentRequestRsProfile
    {
        if ($paymentRequest->isLocked()) {
            throw new AccessDeniedException('The Payment Request can no longer be updated.');
        }

        if ($paymentRequest->checkExpired()) {
            throw new AccessDeniedException('Expiration time for this request have passed.');
        }

        /** @var PaymentRequestRsProfile $prSearcher */
        $prSearcher = $paymentRequest->searchers()->first();

        if (empty($prSearcher)) {
            throw new AccessDeniedException('User not found in this request.');
        }

        try {
            DB::beginTransaction();

            $prSearcher->fill($request->toArray());
            $prSearcher->save();

            /** @var SuperUserProfile $searcherProfile */
            $suProfile = SuperUserProfile::find($paymentRequest->created_by);

            Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));

            $chargeAppFee = $this->getApplicationAmountFee($paymentRequest->amount);
            $chargeDesc = $this->getChargeDescription($paymentRequest, $prSearcher);

            $charge = Charge::create([
                'amount' => $paymentRequest->amount * 100,
                'currency' => StripeService::CURRENCY,
                'source' => $prSearcher->stripe_card_token,
//            'source' => 'tok_visa',
                'application_fee_amount' => $chargeAppFee,
                'destination' => $suProfile->stripe_account_id,
//            'on_behalf_of' => $suProfile->stripe_account_id,
//            'transfer_data' => [
//                'destination' => $suProfile->stripe_account_id,
//            ],
                'description' => $chargeDesc,
            ]);

            $paymentRequest->status = PaymentRequest::STATUS_PAID;
            $paymentRequest->save();

            if ($request->messageId && $message = Message::find($request->messageId)) {
                $message->type = Message::TYPE_PAYMENT_REQUEST_ACCEPTED;
                $message->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $prSearcher;
    }

    public function getChargeDescription(PaymentRequest $paymentRequest, PaymentRequestRsProfile $profile): string
    {
        /** @var PaymentRequestPrice[] $prices */
        $prices = $paymentRequest->prices()->get()->all();

        $pricesDescriptions = [];
        foreach ($prices as $price) {
            $pricesDescriptions[] = PaymentRequest::TYPES[$price->type] . ' $' . $price->price;
        }
        $pricesDescriptions[] = 'application fee $' . $this->getApplicationAmountFee($paymentRequest->amount) / 100;

        $description = 'Charge for room ' . env('FE_URL') . 'room/' . $paymentRequest->room_id .
            ' . Including: ' . implode(', ', $pricesDescriptions) .
            '. Room searcher profile: ' . env('FE_URL') . 'user/' . $profile->rs_profile_id . ' .';

        return $description;
    }

    public function getApplicationAmountFee(int $amount): int
    {
        return 10;
        return (int) (($amount / (100 / PaymentRequest::APPLICATION_FEE_PERCENT)) * 100);
    }

    public function attachFile(PaymentRequest $paymentRequest, int $type): PaymentRequestFiles
    {
        if ($paymentRequest->expired_at && $paymentRequest->checkExpired()) {
            throw new AccessDeniedException('Expiration time for this request have passed.');
        }

        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());

        $storageType = env('FILES_STORAGE_DRIVER');

        $paymentRequestFile = new PaymentRequestFiles();
        $paymentRequestFile->payment_request_id = $paymentRequest->id;
        $paymentRequestFile->type = $type;
        $paymentRequestFile->name = $pInfo['basename'];

        request()->file('file')->storeAs($paymentRequestFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        $paymentRequestFile->save();

        return $paymentRequestFile;
    }

    public function detachFile(PaymentRequestFiles $paymentRequestFiles)
    {
        $filePath = $paymentRequestFiles->getFileNameAttribute();

        $storageType = env('FILES_STORAGE_DRIVER');
        Storage::disk($storageType)->delete($filePath);

        $paymentRequestFiles->delete();

        return true;
    }
}
