<?php

namespace App\Policies;

use App\Models\PropertyRight;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    const RULES = [

        PropertyRight::PROPERTY_OWNER => [
            'property_create',
            'property_update',
            'property_delete',
            'property_view',
            'room_create',
            'room_update',
            'room_delete',
            'create_room_media',
            'update_room_media',
            'delete_room_media',
        ],

        PropertyRight::PROPERTY_MANAGER => [
            'property_update',
            'room_update',
            'room_assign_renter',
            'room_delete_renter',
            'update_room_media',
        ],

        PropertyRight::HEAD_TENANT => [
            'room_assign_renter',
            'room_delete_renter',
        ],

        PropertyRight::ASSISTANT => [

        ],

        PropertyRight::ROOM_RENTER => [

        ],
    ];
}
