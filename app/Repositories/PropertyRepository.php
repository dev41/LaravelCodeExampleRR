<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\PropertyParam;
use App\Models\Room;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class PropertyRepository extends Repository
{
    public static function getById($propertyId)
    {
        $property = DB::table('properties as p')->select([
            'p.id as id',
            'p.creator_id as creator_id',
            'p.title as title',
            'p.lat as lat',
            'p.lon as lon',
            'p.radius as radius',
            'p.address as address',
            'p.transport as transport',
            'p.quite_time as quite_time',

            'u.id as u_id',
            'u.first_name as u_first_name',
            'u.last_name as u_last_name',
            'sup.phone as u_phone',
            'sup.avatar as u_avatar',
        ])->leftJoin('users as u', 'u.id', '=', 'p.creator_id')
            ->leftJoin('super_user_profile as sup', 'sup.id', '=', 'u.id')
            ->where(
            'p.id', '=', $propertyId
        )->first();

        $params = DB::table('property_params')->select([
            'param_id',
            'param_value',
            'element_value',
        ])->where([
            'property_id' => $propertyId,
        ])->get()->toArray();

        $result = self::combineEntityWithParams($property, $params, PropertyParam::FIELDS, Property::PARAMS);

        return empty($result) ? [] : $result;
    }

    public static function getAll(int $userId): array
    {
        $properties = DB::table('properties as p')->select([
            'p.id as id',
            DB::raw('GROUP_CONCAT(DISTINCT p.title) as title'),
            DB::raw('count(r.id) as roomAmount'),
            DB::raw('1 as forthcoming'),
            DB::raw('3 as `empty`'),
        ])
            ->leftJoin('rooms as r', function(JoinClause $leftJoin) {
                $leftJoin
                    ->on('p.id', '=', 'r.property_id')
                    ->on('r.status', '=', DB::raw(Room::STATUS_ACTIVE));
            })
            ->where('p.status', '=', Property::STATUS_ACTIVE)
            ->where('p.creator_id', '=', $userId)
            ->groupBy('p.id')
            ->get()->toArray()
        ;

        return $properties;
    }

}
