<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 * Class UpdatePasswordRequest
 * @package App\Http\Requests\Auth
 *
 * @property string $oldPassword
 * @property string $newPassword
 */
class UpdatePasswordRequest extends Request
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
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8',
        ];
    }
}
