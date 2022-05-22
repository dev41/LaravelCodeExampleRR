<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomParam
 *
 * @package App
 * @property int $id
 * @property int $room_id
 * @property int $type
 * @property string $name
 * @property int $position
 * @property int $isCover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoomMediaFile[] $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Room $room
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia whereIsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoomMedia whereType($value)
 * @mixin \Eloquent
 */
class RoomMedia extends Model
{
    const TYPE_PHOTO = 1;
    const TYPE_FLOOR_PLAN = 2;
    const TYPE_VIDEO = 3;
    const TYPES = [
        self::TYPE_PHOTO => 'photo',
        self::TYPE_FLOOR_PLAN => 'floor_plan',
        self::TYPE_VIDEO => 'video',
    ];

    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'type',
        'name',
        'position',
        'isCover',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(RoomMediaFile::class, 'room_media_id', 'id');
    }
}
