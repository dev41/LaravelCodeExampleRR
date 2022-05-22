<?php

namespace App\Http\Requests\OnboardingPaymentRequest;

use App\Http\Requests\Request;

/**
 * @property string $token
 */
class PayRequest extends Request
{
    public function rules()
    {
        return [
            'token' => 'required|string',
        ];
    }
}
