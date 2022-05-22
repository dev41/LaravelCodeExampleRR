<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Suburb
 *
 * @property int $id
 * @property int $city_id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Suburb whereName($value)
 * @mixin \Eloquent
 */
class Suburb extends Model
{
    protected $table = 'suburb';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id',
    ];
}
