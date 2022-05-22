<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpCities
 *
 * @package App\Models
 * @property int $id
 * @property int $searcher_profile_id
 * @property int $city_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCities whereSearcherProfileId($value)
 * @mixin \Eloquent
 */
class SpCities extends Model
{
    protected $table = 'sp_cities';
    public $timestamps = false;
}
