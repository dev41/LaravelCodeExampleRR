<?php

namespace App\Models;

/**
 * Class OnboardingPaymentRequest
 * @package App\Models
 *
 * @property int $id
 * @property int $creator_id
 * @property int $searcher_id
 * @property int $chat_id
 * @property int $status
 * @property int $expire_in
 * @property int $move_date
 * @property int $rent
 * @property int $bond
 * @property int $created_at
 * @property int $updated_at
 *
 */
class OnboardingPaymentRequest extends BaseModel
{
    protected $table = 'onboarding_payment_request';

    const STATUS_CREATED = 0;
    const STATUS_PAID_BOND = 1;
    const STATUS_PAID_RENT = 2;
    const STATUS_PAID_BOND_RENT = 3;
    const STATUS_DECLINED = 4;

    const PURPOSE_RENT = 1;
    const PURPOSE_BOND = 2;
    const PURPOSE_RENT_BOND = 3;

    protected $fillable = [
        'chat_id',
        'expire_in',
        'move_date',
        'rent',
        'bond',
    ];

    public function isPayable(): bool
    {
        return in_array($this->status, [self::STATUS_CREATED, self::STATUS_PAID_BOND, self::STATUS_PAID_RENT]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(OnboardingPaymentPurchase::class, 'request_id', 'id');
    }

}
