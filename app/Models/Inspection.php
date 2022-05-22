<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Inspection
 *
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inspection whereUserId($value)
 * @mixin \Eloquent
 */
class Inspection extends Model
{
    protected $table = 'inspection';

    const STATUS_CREATED = 0;

    public $fillable = [
        'id',
        'name',
    ];
}
