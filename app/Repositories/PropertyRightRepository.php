<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\User;

class PropertyRightRepository extends Repository
{
    public static function getPermissionsByProperty(Property $property, User $user = null): array
    {
        return [
            'update' => $user ? $user->can('property-update', $property) : false,
        ];
    }

}
