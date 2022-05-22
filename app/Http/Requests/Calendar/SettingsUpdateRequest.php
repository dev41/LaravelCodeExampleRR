<?php

namespace App\Http\Requests\Calendar;

use App\Http\Requests\Request;

/**
 * Class SettingsUpdateRequest
 * @package App\Http\Requests\Calendar
 *
 * @property int $confirmation
 * @property int $visibility_limit
 * @property int $before_booking_limit
 * @property int $viewing_length
 * @property int $break_between_viewing
 * @property int $repeat_period
 * @property string $duration
 * @property array $days
 */
class SettingsUpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            // calendar settings attributes
            // recurring

            'confirmation' => $jsonArray['recurring']['confirmation'] ?? 0,
            'visibility_limit' => $jsonArray['recurring']['calendarVisibilityLimit'] ?? 0,
            'before_booking_limit' => $jsonArray['recurring']['timeLockedBeforeBooking'] ?? 0,

            // viewings

            'viewing_length' => $jsonArray['viewings']['length'] ?? 0,
            'break_between_viewing' => $jsonArray['viewings']['break'] ?? 0,
            'repeat_period' => $jsonArray['viewings']['repeat'] ?? 0,
            'duration' => $this->getDateTimeAttribute($jsonArray['viewings']['duration'] ?? null),

            // days

            'days' => $jsonArray['viewings']['days'] ?? [],
        ];

        $durationNoLimit = (int) ($jsonArray['viewings']['durationNoLimit'] ? $jsonArray['viewings']['durationNoLimit'] === 'true' : 0);

        if ($durationNoLimit) {
            $parameters['duration'] = null;
        }

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'confirmation' => 'required|integer',
            'visibility_limit' => 'required|integer',
            'before_booking_limit' => 'required|integer',
            'viewing_length' => 'required|integer',
            'break_between_viewing' => 'required|integer',
            'repeat_period' => 'required|integer',
            'duration' => 'nullable|date_format:Y-m-d H:i:s',

            'days' => 'required|array',
        ];
    }

}
