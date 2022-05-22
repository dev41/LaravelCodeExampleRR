<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\Request;
use App\Models\RoomMedia;

/**
 * Class CreateMediaRequest
 * @package App\Http\Requests\Room
 *
 * @property integer $type
 */
class CreateMediaRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|int|in:' . implode(',', [RoomMedia::TYPE_PHOTO, RoomMedia::TYPE_FLOOR_PLAN]),
        ];
    }
}
