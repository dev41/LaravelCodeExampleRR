<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\SuperUserProfile;
use App\Models\User;

class SuperUserProfilePolicy extends Policy
{
    public function before(User $user)
    {
        if (!$user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER)) {
            return false;
        }
    }

    public function update(User $user, SuperUserProfile $profile): bool
    {
        return $profile->id === $user->id;
    }
}
