<?php

namespace App\Models;

/**
 * Class SubscriptionPurchase
 * @package App\Models
 *
 * @property int $id
 * @property int $subscription_id
 * @property int $stripe_id
 * @property int $status
 * @property int $amount
 * @property string $details
 * @property int $created_at
 * @property int $updated_at
 */
class SubscriptionPurchase extends BaseModel
{
    protected $table = 'subscription_purchase';

    const STATUS_PAID = 1;
    const STATUS_ERROR = 2;
}
