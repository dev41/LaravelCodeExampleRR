<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\Request;


class UpdateMediaRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position' => 'int|min:0|max:255',
            'isCover' => 'int|in:1,0',
        ];
    }
}
