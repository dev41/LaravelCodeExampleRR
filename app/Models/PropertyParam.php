<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyParam
 *
 * @package App
 * @property int $property_id
 * @property int $param_id
 * @property int $param_value
 * @property int $element_value
 * @property-read \App\Models\Property $property
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam whereElementValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam whereParamValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyParam wherePropertyId($value)
 * @mixin \Eloquent
 */
class PropertyParam extends Model
{
    const FEATURES_ID = 1;
    const RULES_ID = 2;
    const TYPE_ID = 3;
    const PARKING_ID = 4;
    const HEATING_ID = 5;
    const COOLING_ID = 6;
    const FLATMATES_ID = 7;
    const STAY_HOUSE_ID = 8;

    const FIELDS = [
        self::FEATURES_ID => 'features',
        self::RULES_ID => 'rules',
        self::TYPE_ID => 'type',
        self::PARKING_ID => 'parking',
        self::HEATING_ID => 'heating',
        self::COOLING_ID => 'cooling',
        self::FLATMATES_ID => 'flatmates',
        self::STAY_HOUSE_ID => 'stay_house',
    ];

    protected $primaryKey = ['property_id', 'param_id', 'param_value'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'property_id',
        'param_id',
        'param_value',
        'element_value',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'id', 'property_id');
    }
}
