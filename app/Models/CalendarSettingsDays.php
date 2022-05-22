<?php

namespace App\Models;

/**
 * Class CalendarSettingsDays
 *
 * @package App\Models
 * @property int $id
 * @property int $calendar_setting_id
 * @property int $day
 * @property string time_from
 * @property string time_to
 * @property int|null $time_from
 * @property int|null $time_to
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays whereCalendarSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CalendarSettingsDays whereTimeTo($value)
 * @mixin \Eloquent
 */
class CalendarSettingsDays extends BaseModel
{
    protected $table = 'calendar_settings_days';
    public $timestamps = false;

    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    const DAY_SUNDAY = 7;

    const DAY_NAMES = [
        self::DAY_MONDAY => 'monday',
        self::DAY_TUESDAY => 'tuesday',
        self::DAY_WEDNESDAY => 'wednesday',
        self::DAY_THURSDAY => 'thursday',
        self::DAY_FRIDAY => 'friday',
        self::DAY_SATURDAY => 'saturday',
        self::DAY_SUNDAY => 'sunday',
    ];
}
