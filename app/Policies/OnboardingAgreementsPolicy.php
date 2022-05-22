<?php

namespace App\Policies;

use App\Models\OnboardingAgreements;
use App\Models\User;

class OnboardingAgreementsPolicy extends Policy
{
    public function update(User $user, OnboardingAgreements $agreements)
    {
        return $agreements->creator_id == $user->id;
    }

    public function rsUpdate(User $user, OnboardingAgreements $agreements)
    {
        return $agreements->searcher_id == $user->id;
    }
}
