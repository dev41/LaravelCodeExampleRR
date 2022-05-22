<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyRight
 *
 * @package App\Models
 * @property int $property_id
 * @property int $param_id
 * @property int $value
 * @property int $user_id
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyRight whereUserId($value)
 * @mixin \Eloquent
 */
class PropertyRight extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    const ROOM_SEARCHER = 1;
    const PROPERTY_OWNER = 2;
    const PROPERTY_MANAGER = 3;
    const HEAD_TENANT = 4;
    const ASSISTANT = 5;
    const ROOM_RENTER = 6;

    protected $fillable = [
        'property_id',
        'user_id',
        'role_id'
    ];
}
