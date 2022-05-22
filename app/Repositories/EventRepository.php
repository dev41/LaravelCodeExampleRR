<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\RoomMedia;
use App\Models\RoomMediaFile;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class EventRepository extends Repository
{

    public static function getSUViewingEvents(int $userId)
    {
        return self::getViewingEvents()
            ->where(['r.creator_id' => $userId])
            ->get()->all();
    }

    public static function getRSViewingEvents(int $userId)
    {
        return self::getViewingEvents()
            ->where(['e.user_id' => $userId])
            ->get()->all();
    }

    protected static function getViewingEvents()
    {
        return DB::table('event as e')
            ->select(
                'e.id as event_id',
                self::getSelectDateTimeColumn('e.start_at', 'event_date'),
                'e.start_at as event_date_ts',
                'e.length as event_length',
                'e.type as event_type',
                'e.status as event_status',
                'e.creator_id as event_su_id',
                'm.id as message_id',
                'c.id as chat_id',
                'r.id as room_id',
                'r.letter as room_letter',
                DB::raw('group_concat(DISTINCT rmf.name SEPARATOR "|") as room_cover'),
                'r.title as room_title',
                'p.title as property_title',
                'p.address as property_address',
                'u.id as searcher_id',
                'sp.avatar as searcher_avatar',
                'u.first_name as searcher_first_name',
                'u.last_name as searcher_last_name',
                'u.phone_number as searcher_phone'
            )
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->join('searcher_profiles as sp', 'sp.id', '=', 'u.id')
            ->join('messages as m', 'm.id', '=', 'e.message_id')
            ->join('chat as c', 'c.id', '=', 'm.chat_id')
            ->join('rooms as r', 'e.room_id', '=', 'r.id')
            ->join('properties as p', 'r.property_id', '=', 'p.id')
            ->leftJoin('room_media as rm', function (JoinClause $join) {
                $join->on('r.id', '=', 'rm.room_id')
                    ->where('rm.type', '=', RoomMedia::TYPE_PHOTO);
            })
            ->leftJoin('room_media_files as rmf', function (JoinClause $join) {
                $join->on('rm.id', '=', 'rmf.room_media_id')
                    ->where('rmf.resolution', '=', RoomMediaFile::RESOLUTION_DESKTOP);
            })
            ->where([
                'e.type' => Event::TYPE_VIEWING,
            ])
            ->where('e.start_at', '>=', Carbon::now())
            ->whereIn('e.status', [Event::STATUS_CREATED, Event::STATUS_APPROVED])
            ->groupBy('e.id');
    }

}
