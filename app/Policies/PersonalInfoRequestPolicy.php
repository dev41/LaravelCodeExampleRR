<?php

namespace App\Policies;

use App\Models\PersonalInfoRequest;
use App\Models\User;

class PersonalInfoRequestPolicy extends Policy
{
    public function rsUpdate(User $user, PersonalInfoRequest $request)
    {
        return $request->searcher_id === $user->id;
    }
}
