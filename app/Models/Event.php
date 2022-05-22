<?php

namespace App\Models;

/**
 * Class Event
 *
 * @package App\Models
 * @property int $id
 * @property int $creator_id
 * @property int $user_id
 * @property int $room_id
 * @property string $message_id
 * @property int $length
 * @property int $type
 * @property int $status
 * @property string $start_at
 * @property string $created_at
 * @property string $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereUserId($value)
 * @mixin \Eloquent
 */
class Event extends BaseModel
{
    protected $table = 'event';

    const TYPE_VIEWING = 1;

    const STATUS_CREATED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELED = 3;

    const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_APPROVED,
        self::STATUS_CANCELED,
    ];

    const CONFLICT_OK = 0b00000000;
    const CONFLICT_INCOMPATIBLE_SLOT_START = 0b00000001;
    const CONFLICT_INCOMPATIBLE_SLOT_LENGTH = 0b00000010;
}
