<?php

namespace App\Http\Requests\Calendar;

use App\Http\Requests\Request;

/**
 * @property int $roomId
 * @property int $messageId
 * @property int $date
 */
class CreateEventRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'roomId' => 'required|integer',
            'messageId' => 'required|integer',
            'date' => 'required|integer',
        ];
    }

}
