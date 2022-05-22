<?php

namespace App\Formatters;

use App\Models\Property;
use App\Models\Room;

class RoomFormatter extends Formatter
{
    public static function responseObject($roomData, $propertyData, $medias)
    {
        $internetConnectionType = self::formatValue($roomData, 'internet_connection_type', Room::class);
        $internetConnectionTypeValue = $internetConnectionType ? $internetConnectionType['value'] : '';

        $propertyFormatted = PropertyFormatter::responseObject($propertyData);

        $userChatIds = self::getElementValue($roomData, 'user_chat_ids');
        $userChatIds = $userChatIds ? explode(',', $userChatIds) : [];

        $comfort = [];
        self::addParamToArrayIfValueNotEmpty($comfort, 'furnishing', self::formatValue($roomData, 'furnishing', Room::class));
        self::addParamToArrayIfValueNotEmpty($comfort, 'bed', self::formatValue($roomData, 'bed_size', Room::class));
        self::addParamToArrayIfValueNotEmpty($comfort, 'bathroom', self::formatValue($roomData, 'bathroom', Room::class));
        self::addParamToArrayIfValueNotEmpty($comfort, 'kitchen', self::formatValue($roomData, 'kitchenette', Room::class));

        $return = [
            'property' => $propertyFormatted,
            'room' => [
                'id' => self::getElementValue($roomData, 'id'),
                'letter' => self::getElementValue($roomData, 'letter'),

                'userChatIds' => $userChatIds,

                'general' => [
                    'title' => self::getElementValue($roomData, 'title'),
                    'rentAmount' => self::getElementValue($roomData, 'rent_amount'),
                    'rent' => self::formatValue($roomData, 'rent', Room::class)['value'],
                    'consumablesIncluded' => self::getElementValue($roomData, 'consumablesIncluded') === 1,
                    'consumablesValue' => self::getElementValue($roomData, 'consumablesValue'),
                ],
                'consumables' => [
                    'main' => [
                        'water' => self::getElementValue($roomData, 'water') === 1,
                        'electricity' => self::getElementValue($roomData, 'electricity') === 1,
                        'gas' => self::getElementValue($roomData, 'gas') === 1,
                    ],
                    'extras' => self::formatMultiple($roomData, 'consumables_included', Room::class),
                    'servicesIncluded' => self::getElementValue($roomData, 'is_services_included') === 1,
                    'services' => self::formatMultiple($roomData, 'services_included', Room::class),
                ],
                'videoYoutube' => self::getElementValue($roomData, 'videoYoutube'),

                'video' => $medias['video'],
                'images' => $medias['photos'],
                'floorplan' => $medias['floorplan'],

                'story' => self::getElementValue($roomData, 'our_story'),
                'map' => [
                    'address' => self::getElementValue($propertyData, 'address'),
                    'location' => [
                        self::getElementValue($propertyData, 'lat'),
                        self::getElementValue($propertyData, 'lon'),
                    ],
                    'radius' => self::getElementValue($propertyData, 'radius'),
                ],
                'flatemates' => [
                    [
                        'link' => "",
                        'photo' => "",
                        'name' => "",
                        'room' => "A",
                    ],
                    [
                        'link' => "",
                        'photo' => "",
                        'name' => "",
                        'room' => "B",
                    ],
                    [
                        'link' => "",
                        'photo' => "",
                        'name' => "",
                        'room' => "D",
                    ],
                ],
                'features' => [
                    'general' => [
                        'propertyType' => self::formatValue($propertyData, 'type', Property::class),
                    ],
                    'specification' => [
                        'roomType' => self::formatValue($roomData, 'type', Room::class),
                        'roomSize' => self::formatElement($roomData, 'size', Room::class),
                        'bond' => self::formatValue($roomData, 'bond', Room::class),
                        'minRentalPeriod' => self::formatValue($roomData, 'rental_period', Room::class),
                        'dateAvailable' => [
                            'name' => 'Available',
                            'icon' => 'calendar',
                            'value' => self::getElementValue($roomData, 'date_available_text'),
                            'date' => self::getElementValue($roomData, 'date_available'),
                            'tooltip' => '',
                        ],
                    ],
                    'options' => [
                        'flatmates' => [],
                        'transport' => self::formatElement($propertyData, 'transport', Property::class),
                        'parking' => self::formatValue($propertyData, 'parking', Property::class),
                    ],
                    'internet' => [
                        'settings' => [
                            'name' => 'Internet',
                            'icon' => self::formatValue($roomData, 'internet', Room::class)['icon'],
                            'value' => self::formatValue($roomData, 'internet', Room::class)['value'],
                            'speed' => self::getElementValue($roomData, 'internet_speed'),
                            'unlimited' => self::getElementValue($roomData, 'internet_unlimited') === 1,
                            'connectionType' => $internetConnectionTypeValue,
                        ],
                    ],
                    'comfort' => $comfort,
                    'roomFeatures' => self::formatMultiple($roomData, 'features', Room::class),
                    'preferences' => self::formatMultiple($roomData, 'accepting', Room::class),
                ],
            ],
        ];

        return $return;
    }
}
