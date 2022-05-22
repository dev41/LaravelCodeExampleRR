<?php

namespace App\Formatters;

use App\Models\Property;
use App\Repositories\PropertyRightRepository;
use App\Services\SuperUserProfileService;
use Illuminate\Support\Facades\Auth;

class PropertyFormatter extends Formatter
{
    public static function responseObject($propertyData)
    {
        $stayHouse = self::formatValue($propertyData, 'stay_house', Property::class);

        $propertyId = self::getElementValue($propertyData, 'id');
        $permissions = PropertyRightRepository::getPermissionsByProperty(Property::where('id', $propertyId)->firstOrFail(), Auth::user());

        /** @var SuperUserProfileService $profileService */
        $profileService = resolve(SuperUserProfileService::class);
        $avatarFilename = self::getElementValue($propertyData, 'u_avatar');
        $profileId = self::getElementValue($propertyData, 'u_id');
        $userAvatar = $avatarFilename ? '/' . $profileService->getAvatarPathByIdAndName($profileId, $avatarFilename) : '';

        $return = [
            'id' => $propertyId,
            'permissions' => $permissions,
            'assigned' => [],
            'title' => self::getElementValue($propertyData, 'title'),
            'propertyOwnerId' => self::getElementValue($propertyData, 'creator_id'),
            'user' => [
                'id' => self::getElementValue($propertyData, 'u_id'),
                'firstName' => self::getElementValue($propertyData, 'u_first_name'),
                'lastName' => self::getElementValue($propertyData, 'u_last_name'),
                'phone' => self::getElementValue($propertyData, 'u_phone'),
                'avatar' => $userAvatar,
                'responseRate' => '99.9%',
            ],
            'type' => [
                'propertyType' => self::formatValue($propertyData, 'type', Property::class),
            ],
            'options' => [
                'transport' => self::formatElement($propertyData, 'transport', Property::class),
                'parking' => self::formatValue($propertyData, 'parking', Property::class),
            ],
            'map' => [
                'address' => self::getElementValue($propertyData, 'address'),
                'location' => [
                    self::getElementValue($propertyData, 'lat'),
                    self::getElementValue($propertyData, 'lon'),
                ],
                'radius' => self::getElementValue($propertyData, 'radius'),
            ],
            'flatmates' => [
                'total' => '',
                'description' => [
                    'about' => [
                        'name' => 'Flatmates',
                        'icon' => 'flatmates',
                        'value' => 'Easy Going',
                        'tooltip' => '',
                    ],
                ],
                'list' => [
                    [
                        'link' => '',
                        'photo' => '../images/room/faces/1.jpg',
                        'name' => 'Jace L.',
                        'room' => 'A',
                    ],
                    [
                        'link' => '',
                        'photo' => '../images/room/faces/2.jpg',
                        'name' => 'Elly D.',
                        'room' => 'B',
                    ],
                    [
                        'link' => '',
                        'photo' => '../images/room/faces/3.jpg',
                        'name' => 'Harlan F.',
                        'room' => 'D',
                    ],
                ],
            ],
            'features' => [
                'propertyFeatures' => self::formatMultiple($propertyData, 'features', Property::class),
                'stayHouse' => [
                    'name' => $stayHouse['value'],
                    'icon' => $stayHouse['icon'],
                    'value' => '',
                    'tooltip' => '',
                ],
                'heating' => self::formatValue($propertyData, 'heating', Property::class),
                'cooling' => self::formatValue($propertyData, 'cooling', Property::class),
                'basicRules' => self::formatMultiple($propertyData, 'rules', Property::class),
                'quiteTime' => [
                    'name' => 'Quiet time',
                    'icon' => 'quiet-time',
                    'value' => self::getElementValue($propertyData, 'quite_time'),
                    'tooltip' => '',
                ],
            ],
        ];

        return $return;
    }
}
