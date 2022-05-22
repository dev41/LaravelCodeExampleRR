<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentRequest
 *
 * @package App\Models
 * @property int $id
 * @property int $status
 * @property int $purpose
 * @property string $stripe_id
 * @property int $amount
 * @property int $move_in_date
 * @property int $created_by
 * @property int $expired_at
 * @property int $created_at
 * @property int $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereMoveInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $room_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequest whereRoomId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentRequestRsProfile[] $searchers
 * @property-read int|null $searchers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentRequestPrice[] $prices
 * @property-read int|null $prices_count
 */
class PaymentRequest extends Model
{
    protected $table = 'payment_request';

    const APPLICATION_FEE_PERCENT = 10;

    const STATUS_CREATED = 0;
    const STATUS_SENT = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_PAID = 3;
    const STATUS_CANCELED = 4;
    const STATUS_EXPIRED = 5;

    const TYPE_DEPOSIT = 0;
    const TYPE_INITIAL_RENT = 1;
    const TYPE_BOND = 2;
    const TYPE_INITIAL_RENT_BOND = 3;

    const TYPES = [
        self::TYPE_DEPOSIT => 'Deposit payment',
        self::TYPE_INITIAL_RENT => 'Initial rent payment',
        self::TYPE_BOND => 'Bond payment',
        self::TYPE_INITIAL_RENT_BOND => 'Bond payment + Initial rent payment',
    ];

    protected $fillable = [
        'purpose',
        'expired_at',
        'move_in_date',
    ];

    public function isLocked(): bool
    {
        return !in_array($this->status, [
            self::STATUS_SENT,
            self::STATUS_CREATED,
        ]);
    }

    public function isExpired(): bool
    {
        return strtotime($this->expired_at) < time();
    }

    public function checkExpired(): bool
    {
        if ($this->isExpired()) {
            $this->status = PaymentRequest::STATUS_EXPIRED;
            $this->save();

            return true;
        }

        return false;
    }

    public function searchers()
    {
        return $this->hasMany(PaymentRequestRsProfile::class, 'payment_request_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(PaymentRequestPrice::class, 'payment_request_id', 'id');
    }
}
