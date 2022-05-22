<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 * Class LoginRequest
 * @package App\Http\Requests\Auth
 *
 * @property string $email
 * @property string $password
 */
class LoginRequest extends Request
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }
}
