<?php

namespace App\Http\Requests\Inspection;

use App\Http\Requests\Request;

/**
 * @property int $userId
 * @property int $roomId
 */
class CreateRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => 'required|int',
            'roomId' => 'required|int',
        ];
    }
}
