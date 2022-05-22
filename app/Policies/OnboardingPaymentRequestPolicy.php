<?php

namespace App\Policies;

use App\Models\OnboardingAgreement;
use App\Models\OnboardingPaymentRequest;
use App\Models\User;

class OnboardingPaymentRequestPolicy extends Policy
{
    public function suUpdate(User $user, OnboardingPaymentRequest $request)
    {
        return $request->creator_id === $user->id;
    }

    public function getInfo(User $user, OnboardingPaymentRequest $request)
    {
        return $request->creator_id === $user->id || $request->searcher_id === $user->id;
    }

    public function pay(User $user, OnboardingPaymentRequest $request)
    {
        return $request->searcher_id === $user->id;
    }

    public function detachAgreement(User $user, OnboardingAgreement $agreement)
    {
        /** @var OnboardingPaymentRequest $request */
        $request = $agreement->request()->first();
        return $request->creator_id === $user->id;
    }

    public function signAgreement(User $user, OnboardingAgreement $agreement)
    {
        /** @var OnboardingPaymentRequest $request */
        $request = $agreement->request()->first();
        return $request->searcher_id === $user->id;
    }
}
