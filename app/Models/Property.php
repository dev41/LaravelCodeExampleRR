<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Property
 *
 * @package App
 * @property int $id
 * @property int $creator_id
 * @property string $title
 * @property int $lat
 * @property int $lon
 * @property int $radius
 * @property int $type
 * @property int $parking
 * @property string $address
 * @property string $quite_time
 * @property int $status
 * @property string $transport
 * @property int $number_of_flatemates
 * @property string $flatemates_description
 * @property int $created_at
 * @property int $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyRight[] $property_rights
 * @property-read int|null $property_rights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Room[] $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereQuiteTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereTransport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Property whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Property extends Model
{
    const STATUS_TMP = 1;
    const STATUS_ACTIVE = 2;

    const TYPE_HOUSE = 1;
    const TYPE_FLAT = 2;
    const TYPE = [
        self::TYPE_HOUSE => ['name' => 'House', 'icon' => 'property-type'],
        self::TYPE_FLAT => ['name' => 'Flat', 'icon' => 'property-type'],
    ];

    const FEATURES_SWIMMING_POOL = 1;
    const FEATURES_PORT_POOL = 2;
    const FEATURES_COURTYARD = 3;
    const FEATURES_SINGLE_STORY_HOUSE = 4;
    const FEATURES_DOUBLE_STORY_HOUSE = 5;
    const FEATURES_TENNIS_COURTS = 6;
    const FEATURES_GARDENS = 7;
    const FEATURES_UNIVERSAL_ACCESS  = 8;
    const FEATURES_DISABLED_ACCESS = 9;
    const FEATURES = [
        self::FEATURES_SWIMMING_POOL => ['name' => 'Swimming pool', 'icon' => 'swimming-pool'],
        self::FEATURES_PORT_POOL => ['name' => 'Port pool', 'icon' => 'port-pool'],
        self::FEATURES_COURTYARD => ['name' => 'Courtyard', 'icon' => 'courtyard'],
        self::FEATURES_SINGLE_STORY_HOUSE => ['name' => 'Single story house', 'icon' => 'single-stay-house'],
        self::FEATURES_DOUBLE_STORY_HOUSE => ['name' => 'Double story house', 'icon' => 'double-stay-house'],
        self::FEATURES_TENNIS_COURTS => ['name' => 'Tennis courts', 'icon' => 'tennis-courts'],
        self::FEATURES_GARDENS => ['name' => 'Gardens', 'icon' => 'gardens'],
        self::FEATURES_UNIVERSAL_ACCESS => ['name' => 'Universal access', 'icon' => 'universal-access'],
        self::FEATURES_DISABLED_ACCESS => ['name' => 'Disabled access', 'icon' => 'disabled-access'],
    ];

    const RULES_NO_ALCOHOL = 1;
    const RULES_NO_DRUGS = 2;
    const RULES_NO_SMOKING = 3;
    const RULES_NO_WEAPON = 4;
    const RULES_MAKE_NO_NOISE_AFTER_11_PM = 5;
    const QUIET_TIME = 6;
    const RULES = [
        self::RULES_NO_ALCOHOL => ['name' => 'No alcohol', 'icon' => 'no-alcohol'],
        self::RULES_NO_DRUGS => ['name' => 'No drugs', 'icon' => 'no-drugs'],
        self::RULES_NO_SMOKING => ['name' => 'No smoking', 'icon' => 'no-smoking'],
        self::RULES_NO_WEAPON => ['name' => 'No weapon', 'icon' => 'no-weapon'],
        self::RULES_MAKE_NO_NOISE_AFTER_11_PM => ['name' => 'No noise after 11 PM', 'icon' => 'no-noise'],
        self::QUIET_TIME => ['name' => 'Quiet time', 'icon' => 'quiet-time'],
    ];

    const PARKING_OFF_STREET = 1;
    const PARKING_ON_STREET = 2;
    const PARKING_NO = 3;
    const PARKING_COVERED = 4;
    const PARKING_RENTED = 5;
    const PARKING_PERMIT = 6;

    const PARKING = [
        self::PARKING_OFF_STREET => ['name' => 'Off-street parking', 'icon' => 'parking'],
        self::PARKING_ON_STREET => ['name' => 'On-street parking', 'icon' => 'parking'],
        self::PARKING_NO => ['name' => 'No parking', 'icon' => 'parking'],
        self::PARKING_COVERED => ['name' => 'Covered parking', 'icon' => 'parking'],
        self::PARKING_RENTED => ['name' => 'Rented parking', 'icon' => 'parking'],
        self::PARKING_PERMIT => ['name' => 'Permit parking', 'icon' => 'parking'],
    ];

    const STAY_HOUSE_SINGLE = 1;
    const STAY_HOUSE_DOUBLE = 2;

    const STAY_HOUSE = [
        self::STAY_HOUSE_SINGLE => ['name' => 'Single story house', 'icon' => 'single-stay-house'],
        self::STAY_HOUSE_DOUBLE => ['name' => 'Double story house', 'icon' => 'double-stay-house'],
    ];

    const HEATING_CENTRAL = 1;
    const HEATING_PRIVATE = 2;
    const HEATING_SPLIT_SYSTEM = 3;
    const HEATING_DUCTED = 4;
    const HEATING_GAS = 5;
    const HEATING_OIL_HEATERS = 6;
    const HEATING_AIRCONS = 7;
    const HEATING_NONE = 8;

    const HEATING = [
        self::HEATING_CENTRAL => ['name' => 'Central', 'icon' => 'central-heating'],
        self::HEATING_PRIVATE => ['name' => 'Private', 'icon' => 'central-heating'],
        self::HEATING_SPLIT_SYSTEM => ['name' => 'Split system', 'icon' => 'central-heating'],
        self::HEATING_DUCTED => ['name' => 'Ducted', 'icon' => 'central-heating'],
        self::HEATING_GAS => ['name' => 'Gas', 'icon' => 'central-heating'],
        self::HEATING_OIL_HEATERS => ['name' => 'Oil heaters', 'icon' => 'central-heating'],
        self::HEATING_AIRCONS => ['name' => 'Aircons', 'icon' => 'central-heating'],
        self::HEATING_NONE => ['name' => 'None', 'icon' => 'central-heating'],
    ];

    const COOLING_CENTRAL = 1;
    const COOLING_PRIVATE = 2;
    const COOLING_SPLIT_SYSTEM = 3;
    const COOLING_DUCTED = 4;
    const COOLING_GAS = 5;
    const COOLING_OIL_HEATERS = 6;
    const COOLING_AIRCONS = 7;
    const COOLING_NONE = 8;

    const COOLING = [
        self::COOLING_CENTRAL => ['name' => 'Central', 'icon' => 'split-system-cooling'],
        self::COOLING_PRIVATE => ['name' => 'Private', 'icon' => 'split-system-cooling'],
        self::COOLING_SPLIT_SYSTEM => ['name' => 'Split system', 'icon' => 'split-system-cooling'],
        self::COOLING_DUCTED => ['name' => 'Ducted', 'icon' => 'split-system-cooling'],
        self::COOLING_GAS => ['name' => 'Gas', 'icon' => 'split-system-cooling'],
        self::COOLING_OIL_HEATERS => ['name' => 'Oil Heaters', 'icon' => 'split-system-cooling'],
        self::COOLING_AIRCONS => ['name' => 'Aircons', 'icon' => 'split-system-cooling'],
        self::COOLING_NONE => ['name' => 'None', 'icon' => 'split-system-cooling'],
    ];

    const PROPERTIES = [
        'title' => ['type' => 'element', 'name' => 'title', 'icon' => 'title'],
        'lat' => ['type' => 'element', 'name' => 'lat', 'icon' => 'lat'],
        'lon' => ['type' => 'element', 'name' => 'lon', 'icon' => 'lon'],
        'radius' => ['type' => 'element', 'name' => 'Radius', 'icon' => 'radius'],
        'transport' => ['type' => 'element', 'name' => 'Transport', 'icon' => 'transport'],
        'flatmates' => ['type' => 'element', 'name' => 'Flatmates', 'icon' => 'flatmates'],
        'number_of_flatemates' => ['type' => 'element', 'name' => 'Number Of Flatemates', 'icon' => 'number_of_flatemates'],
        'flatemates_description' => ['type' => 'element', 'name' => 'Flatemates Description', 'icon' => 'flatemates_description'],
    ];

    const PARAMS = [
        'type' => ['type' => 'single', 'name' => 'Property type', 'values' => self::TYPE],
        'parking' => ['type' => 'single', 'name' => 'Parking', 'values' => self::PARKING],
        'features' => ['type' => 'multiple', 'name' => 'Features', 'values' => self::FEATURES],
        'rules' => ['type' => 'multiple', 'name' => 'Rules', 'values' => self::RULES],
        'stay_house' => ['type' => 'single', 'name' => 'Stay house', 'values' => self::STAY_HOUSE],
        'heating' => ['type' => 'single', 'name' => 'Heating', 'values' => self::HEATING],
        'cooling' => ['type' => 'single', 'name' => 'Cooling', 'values' => self::COOLING],
    ];

    public $timestamps = true;

    protected $fillable = [
        'title',
        'lat',
        'lon',
        'radius',
        'address',
        'transport',
        'status',
        'number_of_flatemates',
        'flatemates_description',
        'quite_time',
    ];

    public function property_rights()
    {
        return $this->hasMany(PropertyRight::class, 'property_id', 'id');
    }

    public function getUserRole(int $userId): int
    {
        return $this->property_rights()->where('user_id', $userId)->get()->pluck('role_id')->first();
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'property_id', 'id');
    }
}
