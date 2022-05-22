<?php

namespace App\Http\Requests\Chat;

use App\Http\Requests\Request;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Chat
 *
 * @property array $userIds
 * @property string $firstMessage
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
            'userIds' => 'required|array',
            'firstMessage' => 'string',
            'roomId' => 'int|nullable',
        ];
    }
}
