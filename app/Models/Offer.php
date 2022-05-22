<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Offer
 *
 * @package App\Models
 * @property int $id
 * @property int $creator_id
 * @property int $searcher_id
 * @property int $room_id
 * @property int $chat_id
 * @property int $rent_amount
 * @property int $bond
 * @property int $bills_amount
 * @property int $bills_included
 * @property int $move_in_date
 * @property int $expires_in
 * @property int $length_of_lease
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereBillsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereBillsIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereBond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereLengthOfLease($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereMoveInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereSearcherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Offer extends Model
{
    protected $table = 'offer';

    protected $fillable = [
        'rent_amount',
        'bond',
        'bills_amount',
        'bills_included',
        'expires_in',
        'move_in_date',
        'length_of_lease',
        'searcher_id',
        'chat_id',
    ];

    const STATUS_CREATE = 0;
    const STATUS_SEND = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_DECLINED = 3;
    const STATUS_INACTIVE = 4;

    const BILLS_INCLUDE_NO = 0;
    const BILLS_INCLUDE_YES = 1;

    const LEASE_1MONTH = 1;
    const LEASE_2MONTH = 2;
    const LEASE_3MONTH = 3;
    const LEASE_4MONTH = 4;
    const LEASE_6MONTH = 5;
    const LEASE_9MONTH = 6;
    const LEASE_12MONTH = 7;

    const LEASE_MONTH = [
        self::LEASE_1MONTH => 1,
        self::LEASE_2MONTH => 2,
        self::LEASE_3MONTH => 3,
        self::LEASE_4MONTH => 4,
        self::LEASE_6MONTH => 6,
        self::LEASE_9MONTH => 9,
        self::LEASE_12MONTH => 12,
    ];

    const EXPIRES_IN_12HOURS = 1;
    const EXPIRES_IN_24HOURS = 2;
    const EXPIRES_IN_48HOURS = 3;
    const EXPIRES_IN_72HOURS = 4;

    const EXPIRES_IN_HOURS = [
        self::EXPIRES_IN_12HOURS => 12,
        self::EXPIRES_IN_24HOURS => 24,
        self::EXPIRES_IN_48HOURS => 48,
        self::EXPIRES_IN_72HOURS => 72,
    ];

    public function isAcceptable(): bool
    {
        return in_array($this->status, [
            self::STATUS_CREATE,
            self::STATUS_SEND,
        ]);
    }
}
