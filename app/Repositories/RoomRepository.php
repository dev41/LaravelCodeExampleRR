<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomMediaFile;
use App\Models\RoomParam;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class RoomRepository extends Repository
{
    public static function getById($roomId)
    {
        $room = DB::table('rooms')->select(
            'rooms.id',
            'rooms.letter',
            'rooms.property_id',
            'rooms.creator_id',
            'rooms.rent_amount',
            self::getSelectDateTimeColumn('rooms.date_available'),
            'rooms.date_available_text',
            'rooms.size',
            'rooms.title',
            'rooms.our_story',
            'rooms.internet_speed',
            'rooms.internet_unlimited',
            'rooms.consumablesIncluded',
            'rooms.is_services_included',
            'rooms.consumablesValue',
            'rooms.water',
            'rooms.electricity',
            'rooms.gas',
            'rooms.videoYoutube',
            DB::raw('GROUP_CONCAT(DISTINCT uc.user_id) as user_chat_ids')
        )->leftJoin('chat as c', 'c.room_id', '=', 'rooms.id')
        ->leftJoin('user_chat as uc', 'uc.chat_id', '=', 'c.id')
        ->where([
            'rooms.id' => $roomId,
            'rooms.status' => Room::STATUS_ACTIVE,
        ])
        ->groupBy('rooms.id')
        ->first();

        $params = DB::table('room_params')->select([
            'param_id',
            'param_value',
            'element_value',
        ])->where([
            'room_id' => $roomId,
        ])->get()->toArray();

        $result = self::combineEntityWithParams($room, $params, RoomParam::FIELDS, Room::PARAMS);

        return empty($result) ? [] : $result;
    }

    public static function getAllBySU(int $userId): array
    {
        return DB::table('properties as p')
            ->select(
                'r.id as room_id',
                'r.title as room_title',
                'r.letter as room_letter',
                DB::raw('GROUP_CONCAT(DISTINCT rmf.name SEPARATOR "|") as room_cover'),
                'p.title as property_title',
                'p.address as property_address'
            )
            ->join('rooms as r', function (JoinClause $join) {
                $join->on('p.id', '=', 'r.property_id')
                    ->where('r.status', '=', Room::STATUS_ACTIVE);
            })
            ->leftJoin('room_media as rm', 'r.id', '=', 'rm.room_id')
            ->leftJoin('room_media_files as rmf', function (JoinClause $join) {
                $join->on('rmf.room_media_id', '=', 'rm.id')
                    ->where('rmf.resolution', '=', RoomMediaFile::RESOLUTION_THUMBNAIL);
            })
            ->where([
                'p.creator_id' => $userId,
                'p.status' => Property::STATUS_ACTIVE,
            ])
            ->groupBy('r.id')
            ->get()->all();
    }
}
