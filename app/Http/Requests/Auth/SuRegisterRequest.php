<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\Plan;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Subscription
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $plan_id
 * @property integer $room_count
 * @property string $phone_number
 * @property integer $place_for
 * @property string $stripe_token
 */
class SuRegisterRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'first_name' => $jsonArray['firstName'] ?? '',
            'last_name' => $jsonArray['lastName'] ?? '',
            'email' => $jsonArray['email'] ?? '',
            'password' => $jsonArray['password'] ?? '',
            'plan_id' => $jsonArray['planType'] ? Plan::PLAN_STRIPE_IDS[$jsonArray['planType']] : '',
            'room_count' => $jsonArray['roomQty'] ?? null,
            'phone_number' => $jsonArray['phone'] ?? '',
            'place_for' => $jsonArray['placeFor'] ?? null,
            'stripe_token' => $jsonArray['cardToken'] ?? '',
        ];

        $this->replace($parameters);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'plan_id' => 'required|string',
            'room_count' => 'required|integer',
            'phone_number' => 'required|string',
            'place_for' => 'required|integer',
            'stripe_token' => 'string',
        ];
    }
}
