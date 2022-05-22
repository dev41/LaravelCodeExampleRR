<?php

namespace App\Http\Controllers;

use App\Formatters\OnboardingPaymentRequestFormatter;
use App\Http\Requests\OnboardingPaymentRequest\PayRequest;
use App\Http\Requests\OnboardingPaymentRequest\UpdateRequest;
use App\Models\OnboardingAgreement;
use App\Models\OnboardingPaymentRequest;
use App\Models\User;
use App\Repositories\OnboardingPaymentRequestRepository;
use App\Services\OnboardingAgreementService;
use App\Services\OnboardingPaymentRequestService;
use setasign\Fpdi\Tcpdf\Fpdi;

class OnboardingPaymentRequestController extends Controller
{
    public function createTmp(User $searcher, OnboardingPaymentRequestService $paymentRequestService)
    {
        $request = $paymentRequestService->createTmp($searcher);
        $requestData = OnboardingPaymentRequestRepository::getById($request->id);

        return response()->json(OnboardingPaymentRequestFormatter::responseObject($requestData));
    }

    public function update(OnboardingPaymentRequest $request, UpdateRequest $updateRequest, OnboardingPaymentRequestService $paymentRequestService)
    {
        $paymentRequestService->update($request, $updateRequest);
        $requestData = OnboardingPaymentRequestRepository::getById($request->id);

        $requestFormattedData = OnboardingPaymentRequestFormatter::responseObject($requestData);

        return response()->json($requestFormattedData);
    }

    public function getById(OnboardingPaymentRequest $request)
    {
        $requestData = OnboardingPaymentRequestRepository::getById($request->id);

        $data = OnboardingPaymentRequestFormatter::responseObject($requestData);
        $data['stripePublicKey'] = env('STRIPE_PUBLIC_KEY');

        return response()->json($data);
    }

    public function pay(OnboardingPaymentRequest $request, PayRequest $payRequest, OnboardingPaymentRequestService $paymentRequestService)
    {
        $request = $paymentRequestService->pay($request, $payRequest);

        $requestData = OnboardingPaymentRequestRepository::getById($request->id);
        $data = OnboardingPaymentRequestFormatter::responseObject($requestData);

        return response()->json($data);
    }

    public function decline(OnboardingPaymentRequest $request, OnboardingPaymentRequestService $paymentRequestService)
    {
        $request = $paymentRequestService->decline($request);

        $requestData = OnboardingPaymentRequestRepository::getById($request->id);
        $data = OnboardingPaymentRequestFormatter::responseObject($requestData);

        return response()->json($data);
    }

    public function signAgreement(OnboardingAgreement $agreement, OnboardingAgreementService $agreementService)
    {
        $agreement = $agreementService->sign($agreement);
        return response()->json(['file' => $agreement]);
    }

    public function attachAgreement(OnboardingPaymentRequest $request, OnboardingAgreementService $agreementService)
    {
        $agreement = $agreementService->attach($request);
        return response()->json(['file' => $agreement]);
    }

    public function detachAgreement(OnboardingAgreement $agreement, OnboardingAgreementService $agreementService)
    {
        $agreement = $agreementService->detach($agreement);
        return response()->json($agreement);
    }
}
