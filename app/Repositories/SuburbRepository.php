<?php

namespace App\Repositories;

use App\Models\Suburb;

class SuburbRepository extends Repository
{
    public static function findByCityAndName(int $cityId, string $name = null)
    {
        $query = Suburb::where('city_id', '=', $cityId);

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        return $query->get();
    }
}
