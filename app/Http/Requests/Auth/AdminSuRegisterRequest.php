<?php

namespace App\Http\Requests\Auth;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Subscription
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string admin_token
 */
class AdminSuRegisterRequest extends SuRegisterRequest
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'first_name' => $jsonArray['firstName'] ?? '',
            'last_name' => $jsonArray['lastName'] ?? '',
            'email' => $jsonArray['email'] ?? '',
            'password' => $jsonArray['password'] ?? '',
            'phone_number' => $jsonArray['phone'] ?? '',
            'admin_token' => $jsonArray['adminToken'] ?? '',
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
            'phone_number' => 'required|string',
            'admin_token' => 'required|string',
        ];
    }
}
