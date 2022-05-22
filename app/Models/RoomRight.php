<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomRight
 *
 * @package App\Models
 * @property int $room_id
 * @property int $param_id
 * @property int $value
 * @property int $user_id
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomRight whereUserId($value)
 * @mixin \Eloquent
 */
class RoomRight extends Model
{
    const ROOM_SEARCHER = 1;
    const PROPERTY_OWNER = 2;
    const PROPERTY_MANAGER = 3;
    const HEAD_TENANT = 4;
    const ASSISTANT = 5;
    const ROOM_RENTER = 6;

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'room_id',
        'user_id',
        'role_id'
    ];
}
