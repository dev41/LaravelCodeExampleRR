<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomMediaFile
 *
 * @package App\Models
 * @property integer $id
 * @property integer $room_media_id
 * @property integer $resolution
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMediaFile whereRoomMediaId($value)
 * @mixin \Eloquent
 */
class RoomMediaFile extends Model
{
    public $timestamps = false;

    const RESOLUTION_DESKTOP = 1;
    const RESOLUTION_TABLET = 2;
    const RESOLUTION_THUMBNAIL = 3;

    const RESOLUTIONS = [
        self::RESOLUTION_DESKTOP => ['prefix' => '900_520', 'w' => 900, 'h' => 520],
        self::RESOLUTION_THUMBNAIL => ['prefix' => '180_100', 'w' => 180, 'h' => 100],
        self::RESOLUTION_TABLET => ['prefix' => '770_340', 'w' => 770, 'h' => 340],
    ];

    protected $fillable = [
        'room_media_id',
        'resolution',
        'name',
    ];
}
