<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomParam
 *
 * @package App
 * @property int $room_id
 * @property int $param_id
 * @property int $value
 * @property int $param_value
 * @property string|null $element_value
 * @property-read \App\Models\Room $property
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam whereElementValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam whereParamValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomParam whereRoomId($value)
 * @mixin \Eloquent
 */
class RoomParam extends Model
{
    const FEATURES_ID = 1;
    const ACCEPTING_ID = 2;
    const CONSUMABLES_INCLUDED_ID = 3;
    const RENT_ID = 4;
    const BOND_ID = 5;
    const RENTAL_PERIOD_ID = 6;
    const SIZE_UNIT_ID = 7;
    const TYPE_ID = 8;
    const FURNISHING_ID = 9;
    const INTERNET_ID = 10;
    const INTERNET_CONNECTION_TYPE_ID = 11;
    const BATHROOM_ID = 12;
    const KITCHENETTE_ID = 13;
    const CONSUMABLES_ID = 14;
    const BED_SIZE_ID = 15;
    const SERVICES_ID = 18;
    const SERVICES_INCLUDED_ID = 19;

    const FIELDS = [
        self::FEATURES_ID => 'features',
        self::ACCEPTING_ID => 'accepting',
        self::CONSUMABLES_INCLUDED_ID => 'consumables_included',
        self::RENT_ID => 'rent',
        self::BOND_ID => 'bond',
        self::RENTAL_PERIOD_ID => 'rental_period',
        self::SIZE_UNIT_ID => 'size_unit',
        self::TYPE_ID => 'type',
        self::FURNISHING_ID => 'furnishing',
        self::INTERNET_ID => 'internet',
        self::INTERNET_CONNECTION_TYPE_ID => 'internet_connection_type',
        self::BATHROOM_ID => 'bathroom',
        self::KITCHENETTE_ID => 'kitchenette',
        self::CONSUMABLES_ID => 'consumables',
        self::BED_SIZE_ID => 'bed_size',
        self::SERVICES_ID => 'services',
        self::SERVICES_INCLUDED_ID => 'services_included',
    ];

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'room_id',
        'param_id',
        'param_value',
        'element_value'
    ];

    public function property()
    {
        return $this->belongsTo(Room::class, 'id', 'room_id');
    }
}
