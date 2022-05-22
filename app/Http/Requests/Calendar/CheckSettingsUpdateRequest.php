<?php

namespace App\Http\Requests\Calendar;

use App\Http\Requests\Request;

/**
 * @property int $viewing_length
 * @property array $days
 */
class CheckSettingsUpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'viewing_length' => $jsonArray['viewings']['length'] ?? 0,
            'days' => $jsonArray['viewings']['days'] ?? [],
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'viewing_length' => 'required|integer',
            'days' => 'required|array',
        ];
    }

}
