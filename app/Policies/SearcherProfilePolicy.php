<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\SearcherMedia;
use App\Models\SearcherProfile;
use App\Models\User;

class SearcherProfilePolicy extends Policy
{
    public function before(User $user)
    {
        if (!$user->hasAccount(AccountType::ACCOUNT_TYPE_SEARCHER)) {
            return false;
        }
    }

    public function update(User $user, SearcherProfile $profile): bool
    {
        return $profile->id === $user->id;
    }

    public function createMedia(User $user, SearcherProfile $profile): bool
    {
        return $profile->id === $user->id;
    }

    public function deleteMedia(User $user, SearcherMedia $media): bool
    {
        return $media->profile()->first()->id === $user->id;
    }
}
