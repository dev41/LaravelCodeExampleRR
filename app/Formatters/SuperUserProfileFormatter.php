<?php

namespace App\Formatters;

use App\Models\SuperUserProfile;
use App\Services\SuperUserProfileService;

class SuperUserProfileFormatter extends Formatter
{
    public static function responseObject($userData, SuperUserProfile $profile)
    {
        /** @var SuperUserProfileService $profileService */
        $profileService = resolve(SuperUserProfileService::class);

        $result = [
            'id' => self::getElementValue($userData, 'id'),
            'responseRate' => '99.9%',
            'verified' => true,
            'online' => true,
            'firstName' => self::getElementValue($userData, 'first_name'),
            'lastName' => self::getElementValue($userData, 'last_name'),
            'companyAddress' => $profile->company_address,
            'companyName' => $profile->company_name,
            'companyLogo' => $profile->company_logo ? '/' . $profileService->getCompanyLogoPath($profile) : '',
            'phone' => $profile->phone ?? '',
            'socials' => [
                [
                    'name' => 'twitter',
                    'link' => $profile->twitter ?? '',
                ],
                [
                    'name' => 'facebook',
                    'link' => $profile->facebook ?? '',
                ],
                [
                    'name' => 'instagram',
                    'link' => $profile->instagram ?? '',
                ],
                [
                    'name' => 'youtube',
                    'link' => $profile->youtube ?? '',
                ],
            ],
            'avatar' => $profile->avatar ? '/' . $profileService->getAvatarPath($profile) : '',
        ];

        return $result;
    }
}
