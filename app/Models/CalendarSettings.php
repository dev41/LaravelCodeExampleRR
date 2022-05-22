<?php

namespace App\Models;

/**
 * Class CalendarSettings
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $confirmation
 * @property int $before_booking_limit
 * @property int $visibility_limit
 * @property int $repeat_period
 * @property string $duration
 * @property int $viewing_length
 * @property int $break_between_viewing
 * @property string $created_at
 * @property string $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CalendarSettingsDays[] $days
 * @property-read int|null $days_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereBeforeBookingLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereBreakBetweenViewing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereConfirmation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereRepeatPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereViewingLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettings whereVisibilityLimit($value)
 * @mixin \Eloquent
 */
class CalendarSettings extends BaseModel
{
    protected $table = 'calendar_settings';

    const DEFAULT_VIEWING_LENGTH = 30;
    const DEFAULT_BREAK = 5;

    const CONFIRMATION_AUTO = 0;
    const CONFIRMATION_APPROVAL = 1;

    const VISIBILITY_LIMIT_1WEEK = 0;
    const VISIBILITY_LIMIT_2WEEK = 1;
    const VISIBILITY_LIMIT_3WEEK = 2;
    const VISIBILITY_LIMIT_4WEEK = 3;
    const VISIBILITY_LIMIT_NO_LIMIT = 4;

    const BEFORE_BOOKING_LIMIT_1HOUR = 0;
    const BEFORE_BOOKING_LIMIT_2HOUR = 1;
    const BEFORE_BOOKING_LIMIT_4HOUR = 2;
    const BEFORE_BOOKING_LIMIT_6HOUR = 3;

    const REPEAT_PERIOD_WEEKLY = 0;
    const REPEAT_PERIOD_2WEEK = 1;

    protected $fillable = [
        'confirmation',
        'before_booking_limit',
        'visibility_limit',
        'repeat_period',
        'duration',
        'viewing_length',
        'break_between_viewing',
    ];

    public function days()
    {
        return $this->hasMany(CalendarSettingsDays::class, 'calendar_setting_id', 'id');
    }
}
