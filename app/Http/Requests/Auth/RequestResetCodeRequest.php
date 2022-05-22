<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 * Class RequestResetCodeRequest
 * @package App\Http\Requests\Auth
 *
 * @property string $email
 */
class RequestResetCodeRequest extends Request
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
        ];
    }
}
