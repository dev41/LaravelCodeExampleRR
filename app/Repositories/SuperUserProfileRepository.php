<?php
namespace App\Repositories;

use App\Models\SuperUserProfile;

class SuperUserProfileRepository extends Repository
{
    public static function getById(int $profileId): SuperUserProfile
    {
        $profile = SuperUserProfile::find($profileId);

        if (!$profile) {
            self::createEmpty($profileId);
            $profile = self::getById($profileId);
        }

        return $profile;
    }

    public static function createEmpty(int $userId): SuperUserProfile
    {
        $searcherProfile = new SuperUserProfile();
        $searcherProfile->id = $userId;
        $searcherProfile->save();

        return $searcherProfile;
    }
}
