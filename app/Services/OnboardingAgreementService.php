<?php

namespace App\Services;

use App\Libraries\RRFpdi;
use App\Models\OnboardingAgreement;
use App\Models\OnboardingPaymentRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use setasign\FpdiPdfParser\PdfParser\PdfParser;

class OnboardingAgreementService extends Service
{
    public function attach(OnboardingPaymentRequest $request): OnboardingAgreement
    {
        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());

        $storageType = env('FILES_STORAGE_DRIVER');

        $agreement = new OnboardingAgreement();
        $agreement->request_id = $request->id;
        $agreement->name = $pInfo['basename'];
        $agreement->type = (int) request()->post('type');

        request()->file('file')->storeAs($agreement->getFilePathAttribute(), $pInfo['basename'], $storageType);

        $agreement->save();

        return $agreement;
    }

    public function sign(OnboardingAgreement $agreement): OnboardingAgreement
    {
        //https://tcpdf.org/examples/example_052/
        $pdf = new RRFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setPdfParserClass(PdfParser::class);

        $path = base_path('storage/app/public/' . $agreement->getFileNameAttribute());

        $pages = $pdf->setSourceFile($path);

        $info = [
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Testing TCPDF',
            'ContactInfo' => 'http://www.tcpdf.org',
        ];

        $certFilename = 'file://' . base_path('tcpdf.crt');

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);
            $pdf->setSignature($certFilename, $certFilename, '', '', 2, $info);
        }

        $file = Input::file('file');
        $pdf->Image($file->getPathname(), 170, 255, 28, 15);

        $pathInfo = pathinfo($agreement->getFileNameAttribute());
        $newPath = base_path('storage/app/public/' . $pathInfo['dirname'] .
            DIRECTORY_SEPARATOR . $pathInfo['filename'] . '__sign.' . $pathInfo['extension']);
        $pathInfo = pathinfo($newPath);

        $pdf->Output($newPath, 'F');

        $agreementSign = new OnboardingAgreement();
        $agreementSign->request_id = $agreement->request_id;
        $agreementSign->type = OnboardingAgreement::TYPE_SIGNED;
        $agreementSign->name = $pathInfo['basename'];
        $agreementSign->save();

        return $agreementSign;
    }

    public function detach(OnboardingAgreement $agreement)
    {
        $filePath = $agreement->getFileNameAttribute();

        $storageType = env('FILES_STORAGE_DRIVER');
        Storage::disk($storageType)->delete($filePath);

        $agreement->delete();

        return true;
    }
}
