<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SearchProfileParam
 *
 * @package App
 * @property int $profile_id
 * @property int $param_id
 * @property int $param_value
 * @property int $element_value
 * @property-read \App\Models\SearcherProfile $profile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam whereElementValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam whereParamValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfileParam whereProfileId($value)
 * @mixin \Eloquent
 */
class SearcherProfileParam extends Model
{
    const PLACE_FOR_ID = 1;
    const RENTAL_PERIOD_ID = 2;
    const OCCUPANCIES_ID = 3;
    const EMPLOYMENT_STATUS_ID = 4;
    const FURNISHING_ID = 5;
    const PARKING_ID = 6;
    const BATHROOM_ID = 7;
    const KITCHEN_ID = 8;
    const LIFESTYLE_ID = 9;
    const INTERNET_OPTION_ID = 10;
    const PREFERENCES_ID = 11;

    const FIELDS = [
        self::PLACE_FOR_ID => 'place_for',
        self::RENTAL_PERIOD_ID => 'rental_period',
        self::OCCUPANCIES_ID => 'occupancies',
        self::EMPLOYMENT_STATUS_ID => 'employment_status',
        self::FURNISHING_ID => 'furnishing',
        self::PARKING_ID => 'parking',
        self::BATHROOM_ID => 'bathroom',
        self::KITCHEN_ID => 'kitchen',
        self::LIFESTYLE_ID => 'lifestyle',
        self::INTERNET_OPTION_ID => 'internet_option',
        self::PREFERENCES_ID => 'preferences',
    ];

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'profile_id',
        'param_id',
        'param_value',
        'element_value'
    ];

    public function profile()
    {
        return $this->belongsTo(SearcherProfile::class, 'id', 'profile_id');
    }
}
