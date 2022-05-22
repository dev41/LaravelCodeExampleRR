<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpCitySuburbs
 *
 * @package App\Models
 * @property int $sp_city_id
 * @property int $suburb_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCitySuburbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCitySuburbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCitySuburbs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCitySuburbs whereSpCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpCitySuburbs whereSuburbId($value)
 * @mixin \Eloquent
 */
class SpCitySuburbs extends Model
{
    protected $table = 'sp_city_suburbs';
    public $timestamps = false;
}
