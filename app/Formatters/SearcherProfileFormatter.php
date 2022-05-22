<?php

namespace App\Formatters;

use App\Models\SearcherProfile;
use App\Models\User;
use App\Repositories\SearcherMediaFileRepository;

class SearcherProfileFormatter extends Formatter
{
    public static function responseObject($userData, $profileData, $medias)
    {
        $employmentStatuses = self::formatMultiple($profileData, 'employment_status', SearcherProfile::class);
        $employmentStatusOptions = array_map(function ($item) {

            $tooltipRequired = false;
            foreach (SearcherProfile::EMPLOYMENT_STATUS as $status) {
                if ($status['name'] === $item['name']) {
                    $tooltipRequired = $status['tooltipRequired'];
                    break;
                }
            }

            return [
                'name' => $item['name'],
                'tooltipRequired' => $tooltipRequired,
            ];
        }, $employmentStatuses);
        $employmentStatusTooltips = self::getTooltipsFromMultipleParams($employmentStatuses);

        $lifestyleParams = self::formatMultiple($profileData, 'lifestyle', SearcherProfile::class);
        $lifestyleOptions = self::getNameAndIconFromMultipleParams($lifestyleParams);
        $lifestyleTooltips = self::getTooltipsFromMultipleParams($lifestyleParams);

        $internetParams = self::formatMultiple($profileData, 'internet_option', SearcherProfile::class);
        $internetOptions = self::getNameAndIconFromMultipleParams($internetParams);
        $internetTooltips = self::getTooltipsFromMultipleParams($internetParams);

        $avatarUrl = '/' . SearcherMediaFileRepository::getImageFilePath(
            self::getElementValue($profileData, 'id'),
            self::getElementValue($profileData, 'avatar')
        );

        $featureOptions = [];
        self::addParamToArrayIfValueNotEmpty($featureOptions, 'furnishing', self::formatValue($profileData, 'furnishing', SearcherProfile::class));
        self::addParamToArrayIfValueNotEmpty($featureOptions, 'parking', self::formatValue($profileData, 'parking', SearcherProfile::class));
        self::addParamToArrayIfValueNotEmpty($featureOptions, 'bathroom', self::formatValue($profileData, 'bathroom', SearcherProfile::class));
        self::addParamToArrayIfValueNotEmpty($featureOptions, 'kitchen', self::formatValue($profileData, 'kitchen', SearcherProfile::class));

        $result = [
            'id' => self::getElementValue($profileData, 'id'),
            'verified' => true,
            'responseRate' => '99.9%',
            'chatUserIds' => explode(',', self::getElementValue($userData, 'chatUserIds')),
            'socials' => [
                [
                    'name' => 'twitter',
                    'link' => '',
                ],
                [
                    'name' => 'facebook',
                    'link' => '',
                ],
                [
                    'name' => 'instagram',
                    'link' => '',
                ],
                [
                    'name' => 'youtube',
                    'link' => '',
                ],
            ],
            'main' => [
                'phone' => self::getElementValue($userData, 'phone'),
                'firstName' => self::getElementValue($userData, 'first_name'),
                'lastName' => self::getElementValue($userData, 'last_name'),
                'gender' => [
                    'name' => 'Gender',
                    'value' => User::GENDERS[self::getElementValue($userData, 'gender')] ?? User::GENDERS[User::GENDER_MALE],
                ],
                'age' => self::getElementValue($userData, 'age'),
            ],
            'placeFor' => [
                'about' => self::formatValue($profileData, 'place_for', SearcherProfile::class),
                'children' => self::getElementValue($profileData, 'children'),
            ],
            'story' => self::getElementValue($profileData, 'story'),
            'images' => $medias['photos'],
            'avatar' => $avatarUrl,

            'features' => [

                'main' => [
                    'rentAmount' => self::getElementValue($profileData, 'rent_amount'),
                    'rent' => SearcherProfile::RENT[self::getElementValue($profileData, 'rent')],

                    'typical' => [
                        'rentalPeriod' => self::formatValue($profileData, 'rental_period', SearcherProfile::class),
                        'moveDate' => [
                            'name' => 'Move date',
                            'icon' => 'calendar-checkmark',
                            'value' => self::getElementValue($profileData, 'move_date_text'),
                            'date' => date('c', strtotime(self::getElementValue($profileData, 'move_date'))),
                        ],
                        'occupancies' => self::formatValue($profileData, 'occupancies', SearcherProfile::class),
                    ],

                    'employmentStatus' => [
                        'name' => 'Employment status',

                        'options' => $employmentStatusOptions,
                        'tooltips' => $employmentStatusTooltips,
                    ],

                    'location' => [
                        'name' => 'My preferred locations',
                        'options' => !empty($profileData['locations']) ? array_values($profileData['locations']) : [],
                    ],
                ],

                'options' => $featureOptions,

                'lifestyle' => [

                    'set' => $lifestyleOptions,
                    'tooltips' => $lifestyleTooltips,

                    'internet' => [
                        'name' => 'Internet usage',
                        'required' => [
                            'name' => 'Internet',
                            'value' => SearcherProfile::INTERNET[self::getElementValue($profileData, 'internet')],
                        ],
                        'options' => $internetOptions,
                        'tooltips' => $internetTooltips,
                    ],
                ],

                'preferences' => self::formatMultiple($profileData, 'preferences', SearcherProfile::class),

            ],
        ];

        $videoYoutube = self::getElementValue($profileData, 'video_youtube');

        if ($videoYoutube) {
            $result['videoYoutube'] = $videoYoutube;
            $result['video'] = '';
        } else {
            $result['videoYoutube'] = '';
            $result['video'] = $medias['video'];
        }

        return $result;
    }
}
