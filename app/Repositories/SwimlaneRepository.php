<?php

namespace App\Repositories;

use App\Models\PaymentRequest;
use App\Models\PaymentRequestFiles;
use App\Models\Room;
use App\Models\RoomMediaFile;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class SwimlaneRepository extends Repository
{
    const ROOM_MEDIA_FILENAME_SEPARATOR = '#';

    const STATUS_VIEWING = 'Viewing';
    const STATUS_PAYMENT = 'Payment';
    const STATUS_MOVING = 'Movingin';
    const STATUS_ROOM_RENTING = 'Roomrenting';
    const STATUS_MOVING_OUT = 'Movingout';
    const STATUS_AGREEMENT = 'Agreement';

    public static function getAllRooms(int $userId): array
    {
        $rooms = DB::table('rooms as r')
            ->select(
                'r.id as roomId',
                'r.letter as roomLetter',
                DB::raw('group_concat(DISTINCT rmf.name ORDER BY rmf.id SEPARATOR "' . self::ROOM_MEDIA_FILENAME_SEPARATOR . '") AS roomCover'),
                'p.title as propertyTitle',
                'p.address as address'
            )
            ->join('properties as p', 'p.id', '=', 'r.property_id')
            ->leftJoin('room_media as rm', 'rm.room_id', '=', 'r.id')
            ->leftJoin('room_media_files as rmf', function (JoinClause $join) {
                $join->on('rmf.room_media_id', '=', 'rm.id')
                    ->where(['rmf.resolution' => RoomMediaFile::RESOLUTION_THUMBNAIL]);
            })
            ->where([
                'p.creator_id' => $userId,
                'r.status' => Room::STATUS_ACTIVE,
            ])
            ->groupBy('r.id')
            ->get()->all();

        return $rooms;
    }

    public static function getPaymentRequestsByRooms(array $roomIds): array
    {
        $requests = DB::table('payment_request as pr')
            ->select(
                'pr.id as id',
                'pr.room_id as room_id',
                'pr.purpose as purpose',
                'prp.type as price_type',
                self::getSelectDateTimeColumn('pr.expired_at', 'expiration'),
                self::getSelectDateTimeColumn('pr.move_in_date', 'move_in_date'),
                'pr.status as status',
                'prp.price as price',
                'prp.type as type',
                DB::raw('GROUP_CONCAT(DISTINCT prrp.rs_profile_id) as user_id'),
                DB::raw('GROUP_CONCAT(DISTINCT prrp.first_name) as user_first_name'),
                DB::raw('GROUP_CONCAT(DISTINCT prrp.last_name) as user_last_name'),
                DB::raw('GROUP_CONCAT(DISTINCT prrp.middle_name) as user_middle_name'),
                DB::raw('GROUP_CONCAT(DISTINCT prrp.birthday) as user_birthday'),
                DB::raw('GROUP_CONCAT(DISTINCT prf.name SEPARATOR "' . SwimlaneRepository::ROOM_MEDIA_FILENAME_SEPARATOR . '") as medias')
            )
            ->from('payment_request as pr')
            ->join('payment_request_price as prp', 'pr.id', '=', 'prp.payment_request_id')
            ->join('payment_request_rs_profile as prrp', 'pr.id', '=', 'prrp.payment_request_id')
            ->leftJoin('payment_request_files as prf', function (JoinClause $join) {
                $join->on('prf.payment_request_id', '=', 'pr.id')
                    ->where('prf.type', '=', PaymentRequestFiles::TYPE_SEARCHER_INFO);
            })
            ->whereNotIn('pr.status', [PaymentRequest::STATUS_ACCEPTED])
            ->whereIn('pr.room_id', $roomIds)
            ->groupBy('prp.id')
            ->get()->all();

        return $requests;
    }
}
