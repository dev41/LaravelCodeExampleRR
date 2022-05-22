<?php

namespace App\Http\Requests\Calendar;

use App\Http\Requests\Request;

/**
 * @property array $events
 */
class UpdateViewingStatusRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'events' => 'required|array',
        ];
    }

}
