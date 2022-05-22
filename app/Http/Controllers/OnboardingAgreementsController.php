<?php

namespace App\Http\Controllers;

use App\Formatters\OnboardingAgreementsFormatter;
use App\Http\Requests\OnboardingAgreements\CreateTmpRequest;
use App\Http\Requests\OnboardingAgreements\DeclineRequest;
use App\Http\Requests\OnboardingAgreements\UpdateRequest;
use App\Models\OnboardingAgreements;
use App\Repositories\OnboardingAgreementsRepository;
use App\Services\OnboardingAgreementsService;

class OnboardingAgreementsController extends Controller
{

    public function createTmp(CreateTmpRequest $request, OnboardingAgreementsService $agreementsService)
    {
        $agreements = $agreementsService->createTmp($request);
        $agreementsData = OnboardingAgreementsRepository::getById($agreements->id);

        return response()->json(OnboardingAgreementsFormatter::responseObject($agreementsData));
    }

    public function update(OnboardingAgreements $agreements, UpdateRequest $request, OnboardingAgreementsService $agreementsService)
    {
        $agreements = $agreementsService->update($agreements, $request);
        $agreementsData = OnboardingAgreementsRepository::getById($agreements->id);

        return response()->json(OnboardingAgreementsFormatter::responseObject($agreementsData));
    }

    public function decline(OnboardingAgreements $agreements, DeclineRequest $request, OnboardingAgreementsService $agreementsService)
    {
        $agreementsService->decline($agreements, $request);

        return response()->json(['success' => true]);
    }

    public function uploadAgreement(OnboardingAgreements $agreements, OnboardingAgreementsService $agreementsService)
    {
        $file = $agreementsService->uploadAgreement($agreements);

        return response()->json(['file' => $file]);
    }

    public function uploadHouseRules(OnboardingAgreements $agreements, OnboardingAgreementsService $agreementsService)
    {
        $file = $agreementsService->uploadHouseRules($agreements);

        return response()->json(['file' => $file]);
    }

    public function signDocuments(OnboardingAgreements $agreements, OnboardingAgreementsService $agreementsService)
    {
        $file = $agreementsService->signDocuments($agreements);

        return response()->json(['success' => true]);
    }

    public function signSearcherDocuments(OnboardingAgreements $agreements, OnboardingAgreementsService $agreementsService)
    {
        $file = $agreementsService->signSearcherDocuments($agreements);

        return response()->json(['success' => true]);
    }
}
