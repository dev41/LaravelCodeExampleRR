<?php

namespace App\Models;

/**
 * Class OnboardingPaymentPurchase
 * @package App\Models
 *
 * @property int $id
 * @property int $request_id
 * @property int $payment_intent_id
 * @property int $pi_client_secret
 * @property int $status
 * @property int $type
 * @property int $amount
 * @property int $created_at
 * @property int $updated_at
 */
class OnboardingPaymentPurchase extends BaseModel
{
    protected $table = 'onboarding_payment_purchase';

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;

    const TYPE_RENT = 1;
    const TYPE_BOND = 2;
    const TYPE_RENT_BOND = 3;
}
