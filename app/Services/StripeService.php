<?php

namespace App\Services;

use App\Models\SuperUserProfile;
use Stripe\OAuth;
use Stripe\Stripe;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class StripeService extends Service
{
    const CURRENCY = 'USD';

    public function connectAccount(string $state, string $code)
    {
        /** @var SuperUserProfile $suProfile */
        $suProfile = SuperUserProfile::where(['stripe_state' => $state])->first();

        if (!$suProfile) {
            throw new InvalidParameterException('Superuser not found.');
        }

        if ($suProfile->stripe_account_id) {
            throw new InvalidParameterException('Stripe connect account for this user already exist.');
        }

        Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));

        $response = OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        $suProfile->stripe_account_id = $response->stripe_user_id;
        $suProfile->save();
    }
}
