<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 * Class RequestSuperuser
 * @package App\Http\Requests\Auth
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone_number
 * @property boolean $owner_account
 */
class RequestSuperuser extends Request
{
    const TEMPORARY_PASSWORD = '12345678';

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = $this->post();
        $data['owner_account'] = true;
        $data['password'] = self::TEMPORARY_PASSWORD;

        $this->replace($data);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|max:50',
            'owner_account' => 'boolean',
            'password' => 'required|string|min:8',
        ];
    }
}
