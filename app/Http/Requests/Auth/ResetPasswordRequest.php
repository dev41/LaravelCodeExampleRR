<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 * Class ResetPasswordRequest
 * @package App\Http\Requests\Auth
 *
 * @property string $token
 * @property string $password
 */
class ResetPasswordRequest extends Request
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
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ];
    }
}
