<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\User;

class CommonPolicy extends Policy
{
    public function hasSuperuserAccount(User $user): bool
    {
        return $user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER);
    }

    public function hasSearcherAccount(User $user): bool
    {
        return $user->hasAccount(AccountType::ACCOUNT_TYPE_SEARCHER);
    }
}
