<?php

namespace App\Repositories;

use App\Models\RoomParam;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class OfferRepository extends Repository
{
    public static function getById(int $offerId)
    {
        $data = DB::table('offer as o')
            ->select(
                'o.id',
                'o.bond',
                'o.rent_amount',
                'o.bills_amount',
                'o.bills_included',
                self::getSelectDateTimeColumn('o.move_in_date'),
                'o.expires_in',
                'o.length_of_lease',
                'o.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT rp.param_value SEPARATOR "|") as rent_period')
            )
            ->leftJoin('room_params as rp', function (JoinClause $join) {
                $join->on('rp.room_id', '=', 'o.room_id')
                    ->where('rp.param_id', '=', RoomParam::RENT_ID);
            })
            ->where('o.id', '=', $offerId)
            ->groupBy('o.id')
            ->first();

        return $data;
    }
}
