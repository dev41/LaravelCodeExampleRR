<?php

namespace App\Formatters;

use App\Repositories\RoomMediaFileRepository;

class ChatAvailableRoomsFormatter extends Formatter
{
    public static function responseObject(array $rooms): array
    {
        $formattedRooms = [];

        foreach ($rooms as $roomData) {

            $roomCover = explode('|', $roomData->room_cover ?? '');
            $roomCover = empty($roomCover) ? '' :
                RoomMediaFileRepository::getImageFilePath($roomData->room_id, reset($roomCover));

            $formattedRooms[] = [
                'roomId' => $roomData->room_id,
                'roomLetter' => $roomData->room_letter,
                'roomCover' => $roomCover,
                'roomTitle' => $roomData->room_title,
                'propertyTitle' => $roomData->property_title,
                'address' => $roomData->property_address,
            ];
        }

        return $formattedRooms;
    }
}
