<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\User;

/**
 * Class UserUpdateRequest
 * @package App\Http\Requests\Auth
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $gender
 * @property string $age
 * @property string $employment_status
 * @property string $story
 */
class UserUpdateRequest extends Request
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
            'email' => 'email|unique:users',
            'first_name' => 'string|max:50',
            'last_name' => 'string|max:50',
            'phone_number' => 'string|max:50',
        ];
    }
}
