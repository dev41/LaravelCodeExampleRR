<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Room
 *
 * @package App
 * @property int $id
 * @property int $property_id
 * @property int $creator_id
 * @property string $letter
 * @property int $rent_amount
 * @property \DateTime $date_available
 * @property int $size
 * @property string $title
 * @property string $our_story
 * @property string $internet_speed
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $date_available_text
 * @property int|null $internet_unlimited
 * @property int|null $consumablesIncluded
 * @property string|null $consumablesValue
 * @property int|null $is_services_included
 * @property int|null $water
 * @property int|null $electricity
 * @property int|null $gas
 * @property string|null $videoYoutube
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoomMedia[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Property $property
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoomRight[] $rights
 * @property-read int|null $rights_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereConsumablesIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereConsumablesValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereDateAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereDateAvailableText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereInternetSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereInternetUnlimited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereIsServicesIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereOurStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereVideoYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereWater($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $creator
 */
class Room extends Model
{
    const STATUS_TMP = 1;
    const STATUS_ACTIVE = 2;

    const RENT_DAILY = 1;
    const RENT_WEEKLY = 2;
    const RENT_MONTHLY = 3;
    const RENT = [
        self::RENT_DAILY => ['name' => 'day', 'icon' => 'day'],
        self::RENT_WEEKLY => ['name' => 'week', 'icon' => 'week'],
        self::RENT_MONTHLY => ['name' => 'month', 'icon' => 'month'],
    ];

    const RENT_LABELS = [
        self::RENT_DAILY => 'daily',
        self::RENT_WEEKLY => 'weekly',
        self::RENT_MONTHLY => 'monthly',
    ];

    const BOND_NO = 1;
    const BOND_1_WEEK = 2;
    const BOND_2_WEEK = 3;
    const BOND_3_WEEK = 4;
    const BOND_4_WEEK = 5;
    const BOND = [
        self::BOND_NO => ['name' => 'No bond', 'icon' => 'bond'],
        self::BOND_1_WEEK => ['name' => '1 week', 'icon' => 'bond'],
        self::BOND_2_WEEK => ['name' => '2 weeks', 'icon' => 'bond'],
        self::BOND_3_WEEK => ['name' => '3 weeks', 'icon' => 'bond'],
        self::BOND_4_WEEK => ['name' => '4 weeks', 'icon' => 'bond'],
    ];

    const RENTAL_PERIOD_1_WEEK = 2;
    const RENTAL_PERIOD_2_WEEK = 3;
    const RENTAL_PERIOD_1_MONTHS = 4;
    const RENTAL_PERIOD_2_MONTHS = 5;
    const RENTAL_PERIOD_3_MONTHS = 6;
    const RENTAL_PERIOD_4_MONTHS = 7;
    const RENTAL_PERIOD_6_MONTHS = 8;
    const RENTAL_PERIOD_9_MONTHS = 9;
    const RENTAL_PERIOD_12_MONTHS = 10;
    const RENTAL_PERIOD = [
        self::RENTAL_PERIOD_1_WEEK => ['name' => '1 week', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_2_WEEK => ['name' => '2 weeks', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_1_MONTHS => ['name' => '1 month', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_2_MONTHS => ['name' => '2 months', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_3_MONTHS => ['name' => '3 months', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_4_MONTHS => ['name' => '4 months', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_6_MONTHS => ['name' => '6 months', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_9_MONTHS => ['name' => '9 months', 'icon' => 'min-rental-period'],
        self::RENTAL_PERIOD_12_MONTHS => ['name' => '12 months', 'icon' => 'min-rental-period'],
    ];

    const ROOM_SIZE_UNIT_SQM = 1;
    const ROOM_SIZE_UNIT_FT = 2;
    const ROOM_SIZE_UNIT = [
        self::ROOM_SIZE_UNIT_SQM => ['name' => 'sqm', 'icon' => 'sqm'],
        self::ROOM_SIZE_UNIT_FT => ['name' => 'ft', 'icon' => 'ft'],
    ];

    const TYPE_PRIVATE = 1;
    const TYPE_SHARED = 2;
    const TYPE_COUPLE = 3;
    const TYPE = [
        self::TYPE_PRIVATE => ['name' => 'Private', 'icon' => 'room-type'],
        self::TYPE_SHARED => ['name' => 'Shared', 'icon' => 'room-type'],
        self::TYPE_COUPLE => ['name' => 'Couple', 'icon' => 'room-type'],
    ];

    const FURNISHING_FURNISHED = 1;
    const FURNISHING_UNFURNISHED = 2;
    const FURNISHING_RENTED = 3;
    const FURNISHING = [
        self::FURNISHING_FURNISHED => ['name' => 'Furnished', 'icon' => 'furnishings'],
        self::FURNISHING_UNFURNISHED => ['name' => 'Unfurnished', 'icon' => 'furnishings'],
        self::FURNISHING_RENTED => ['name' => 'Rented', 'icon' => 'furnishings'],
    ];

    const FEATURES_BEDSIDE_TABLE = 1;
    const FEATURES_WARDROBE = 2;
    const FEATURES_DRAWERS = 3;
    const FEATURES_AIR_CONDITIONER = 4;
    const FEATURES_HEATER = 5;
    const FEATURES_DESK = 6;
    const FEATURES_LAMP = 7;
    const FEATURES_CHAIR = 8;
    const FEATURES_COUCH = 9;
    const FEATURES_TV = 10;
    const FEATURES_INTERNET = 11;
    const FEATURES_BALCONY = 12;
    const FEATURES_DOOR_LOCK = 13;
    const FEATURES_KITCHENETTE = 14;
    const FEATURES_WASH_MACHINE = 15;
    const FEATURES = [
        self::FEATURES_BEDSIDE_TABLE => ['name' => 'Bedside table', 'icon' => 'bedside-table'],
        self::FEATURES_WARDROBE => ['name' => 'Wardrobe', 'icon' => 'wardrobe'],
        self::FEATURES_DRAWERS => ['name' => 'Drawers', 'icon' => 'drawers'],
        self::FEATURES_AIR_CONDITIONER => ['name' => 'Air conditioner', 'icon' => 'air-conditioner'],
        self::FEATURES_HEATER => ['name' => 'Heater', 'icon' => 'heater'],
        self::FEATURES_DESK => ['name' => 'Desk', 'icon' => 'desk'],
        self::FEATURES_LAMP => ['name' => 'Lamp', 'icon' => 'lamp'],
        self::FEATURES_CHAIR => ['name' => 'Chair', 'icon' => 'chair'],
        self::FEATURES_COUCH => ['name' => 'Couch', 'icon' => 'couch'],
        self::FEATURES_TV => ['name' => 'TV', 'icon' => 'tv'],
        self::FEATURES_INTERNET => ['name' => 'Internet', 'icon' => 'wifi'],
        self::FEATURES_BALCONY => ['name' => 'Balcony', 'icon' => 'balcony'],
        self::FEATURES_DOOR_LOCK => ['name' => 'Door lock', 'icon' => 'door-lock'],
        self::FEATURES_KITCHENETTE => ['name' => 'Kitchen', 'icon' => 'kitchenette'],
        self::FEATURES_WASH_MACHINE => ['name' => 'Wash machine', 'icon' => 'wash-machine'],
    ];

    const INTERNET_NO = 1;
    const INTERNET_NOT_INCLUDED = 2;
    const INTERNET_INCLUDED = 3;
    const INTERNET_UNLIMITED = 4;
    const INTERNET = [
        self::INTERNET_NO => ['name' => 'No internet', 'icon' => 'wifi'],
        self::INTERNET_NOT_INCLUDED => ['name' => 'Not included', 'icon' => 'wifi'],
        self::INTERNET_INCLUDED => ['name' => 'Included', 'icon' => 'wifi'],
        self::INTERNET_UNLIMITED => ['name' => 'Unlimited included in rent', 'icon' => 'wifi'],
    ];

    const INTERNET_TYPE_ISDN = 1;
    const INTERNET_TYPE_BISDN = 2;
    const INTERNET_TYPE_DSL = 3;
    const INTERNET_TYPE_ADSL = 4;
    const INTERNET_TYPE_ADSL2 = 5;
    const INTERNET_TYPE_SDSL = 6;
    const INTERNET_TYPE_VDSL = 7;
    const INTERNET_TYPE_WIRELESS = 8;
    const INTERNET_TYPE = [
        self::INTERNET_TYPE_ISDN => ['name' => 'ISDN', 'icon' => 'wifi'],
        self::INTERNET_TYPE_BISDN => ['name' => 'B-ISDN', 'icon' => 'wifi'],
        self::INTERNET_TYPE_DSL => ['name' => 'DSL', 'icon' => 'wifi'],
        self::INTERNET_TYPE_ADSL => ['name' => 'ADSL', 'icon' => 'wifi'],
        self::INTERNET_TYPE_ADSL2 => ['name' => 'ADSL+2', 'icon' => 'wifi'],
        self::INTERNET_TYPE_SDSL => ['name' => 'SDSL', 'icon' => 'wifi'],
        self::INTERNET_TYPE_VDSL => ['name' => 'VDSL', 'icon' => 'wifi'],
        self::INTERNET_TYPE_WIRELESS => ['name' => 'WiFi', 'icon' => 'wifi'],
    ];

    const ACCEPTING_SMOKING = 1;
    const ACCEPTING_STUDENT = 2;
    const ACCEPTING_PETS = 3;
    const ACCEPTING_CHILDREN = 4;
    const ACCEPTING_LGBT = 5;
    const ACCEPTING_40_YEARS_OLDS = 6;
    const ACCEPTING_RETIREES = 7;
    const ACCEPTING_WELFARE = 8;
    const ACCEPTING_INTERNATIONAL_STUDENTS = 9;
    const ACCEPTING_LOCAL_STUDENTS = 10;
    const ACCEPTING_NON_ABLE_BODIED = 11;
    const ACCEPTING = [
        self::ACCEPTING_SMOKING => ['name' => 'Smoking', 'icon' => 'smoking'],
        self::ACCEPTING_STUDENT => ['name' => 'Student', 'icon' => 'student'],
        self::ACCEPTING_PETS => ['name' => 'Pet', 'icon' => 'pets'],
        self::ACCEPTING_CHILDREN => ['name' => 'Children', 'icon' => 'children'],
        self::ACCEPTING_LGBT => ['name' => 'LGBT', 'icon' => 'lgbt'],
        self::ACCEPTING_40_YEARS_OLDS => ['name' => '40+ years olds', 'icon' => 'years-olds'],
        self::ACCEPTING_RETIREES => ['name' => 'Retirees', 'icon' => 'retirees'],
        self::ACCEPTING_WELFARE => ['name' => 'Welfare', 'icon' => 'welfare'],
        self::ACCEPTING_INTERNATIONAL_STUDENTS => ['name' => 'International student', 'icon' => 'international-students'],
        self::ACCEPTING_LOCAL_STUDENTS => ['name' => 'Local student', 'icon' => 'local-students'],
        self::ACCEPTING_NON_ABLE_BODIED => ['name' => 'Non-able-bodied', 'icon' => 'non-able-bodied'],
    ];

    const BATHROOM_SHARED = 1;
    const BATHROOM_PRIVATE = 2;
    const BATHROOM_ENSUITE = 3;
    const BATHROOM = [
        self::BATHROOM_SHARED => ['name' => 'Shared', 'icon' => 'bathroom'],
        self::BATHROOM_PRIVATE => ['name' => 'Private', 'icon' => 'bathroom'],
        self::BATHROOM_ENSUITE => ['name' => 'Ensuite required', 'icon' => 'bathroom'],
    ];

    const KITCHENETTE_SHARED = 1;
    const KITCHENETTE_PRIVATE = 2;
    const KITCHENETTE = [
        self::KITCHENETTE_SHARED => ['name' => 'Shared', 'icon' => 'kitchenette'],
        self::KITCHENETTE_PRIVATE => ['name' => 'Private', 'icon' => 'kitchenette'],
    ];

    const CONSUMABLES_IS_INCLUDED = 1;
    const CONSUMABLES_NOT_INCLUDED = 2;
    const CONSUMABLES = [
        self::CONSUMABLES_IS_INCLUDED => ['name' => 'Included', 'icon' => 'included'],
        self::CONSUMABLES_NOT_INCLUDED => ['name' => 'Excluded', 'icon' => 'excluded'],
    ];

    const CONSUMABLES_INCLUDED_TOILET_PAPER = 1;
    const CONSUMABLES_INCLUDED_WASH_POWDER = 2;
    const CONSUMABLES_INCLUDED_HAND_WASH = 3;
    const CONSUMABLES_INCLUDED_HOUSE_CLEAN = 4;
    const CONSUMABLES_INCLUDED_DISH_LIQUID = 5;
    const CONSUMABLES_INCLUDED_GARDENING = 6;
    const CONSUMABLES_INCLUDED = [
        self::CONSUMABLES_INCLUDED_TOILET_PAPER => ['name' => 'Toilet paper', 'icon' => 'toilet-paper'],
        self::CONSUMABLES_INCLUDED_WASH_POWDER => ['name' => 'Wash powder', 'icon' => 'wash-powder'],
        self::CONSUMABLES_INCLUDED_HAND_WASH => ['name' => 'Hand wash', 'icon' => 'hand-wash'],
        self::CONSUMABLES_INCLUDED_HOUSE_CLEAN => ['name' => 'House clean', 'icon' => 'house-clean'],
        self::CONSUMABLES_INCLUDED_DISH_LIQUID => ['name' => 'Dish liquid', 'icon' => 'dish-liquid'],
        self::CONSUMABLES_INCLUDED_GARDENING => ['name' => 'Wash soap', 'icon' => 'wash-soap'],
    ];

    const BED_SIZE_SINGLE = 1;
    const BED_SIZE_DOUBLE = 2;
    const BED_SIZE_QUEEN = 3;
    const BED_SIZE_KING = 4;
    const BED_SIZE_NONE = 5;
    const BED_SIZE_KING_SINGLE = 6;
    const BED_SIZE = [
        self::BED_SIZE_SINGLE => ['name' => 'Single', 'icon' => 'bed'],
        self::BED_SIZE_DOUBLE => ['name' => 'Double', 'icon' => 'bed'],
        self::BED_SIZE_QUEEN => ['name' => 'Queen', 'icon' => 'bed'],
        self::BED_SIZE_KING => ['name' => 'King', 'icon' => 'bed'],
        self::BED_SIZE_NONE => ['name' => 'None', 'icon' => 'bed'],
        self::BED_SIZE_KING_SINGLE => ['name' => 'King single', 'icon' => 'bed'],
    ];

    const SERVICES_IS_INCLUDED = 1;
    const SERVICES_NOT_INCLUDED = 2;
    const SERVICES = [
        self::SERVICES_IS_INCLUDED => ['name' => 'Included', 'icon' => 'included'],
        self::SERVICES_NOT_INCLUDED => ['name' => 'Excluded', 'icon' => 'excluded'],
    ];

    const SERVICES_INCLUDED_HOUSE_CLEAN = 1;
    const SERVICES_INCLUDED_GARDENING = 2;
    const SERVICES_INCLUDED_CLEANERS = 3;
    const SERVICES_INCLUDED = [
        self::SERVICES_INCLUDED_HOUSE_CLEAN => ['name' => 'Maintenances', 'icon' => 'maintances'],
        self::SERVICES_INCLUDED_GARDENING => ['name' => 'Gardening', 'icon' => 'gardening'],
        self::SERVICES_INCLUDED_CLEANERS => ['name' => 'Cleaners', 'icon' => 'cleaners'],
    ];

    const PROPERTIES = [
        'rent_amount' => ['type' => 'element', 'name' => 'Rent Amount', 'icon' => 'rent_amount'],
        'date_available' => ['type' => 'element', 'name' => 'Date Available', 'icon' => 'date_available'],
        'size' => ['type' => 'element', 'name' => 'Room size', 'icon' => 'room-size'],
        'title' => ['type' => 'element', 'name' => 'Title', 'icon' => 'title'],
        'our_story' => ['type' => 'element', 'name' => 'Our Story', 'icon' => 'our_story'],
        'internet_speed' => ['type' => 'element', 'name' => 'Internet Speed', 'icon' => 'internet_speed'],
        'internet_unlimited' => ['type' => 'element', 'name' => 'Internet Unlimited', 'icon' => 'internet_unlimited'],
    ];

    const PARAMS = [
        'rent' => ['type' => 'single', 'name' => 'Rent', 'values' => self::RENT],
        'bond' => ['type' => 'single', 'name' => 'Bond', 'values' => self::BOND],
        'rental_period' => ['type' => 'single', 'name' => 'Min. rental period', 'values' => self::RENTAL_PERIOD],
        'size_unit' => ['type' => 'single', 'name' => 'Size Unit', 'values' => self::ROOM_SIZE_UNIT],
        'type' => ['type' => 'single', 'name' => 'Room type', 'values' => self::TYPE],
        'furnishing' => ['type' => 'single', 'name' => 'Furnishing', 'values' => self::FURNISHING],
        'internet' => ['type' => 'single', 'name' => 'Internet', 'values' => self::INTERNET],
        'internet_connection_type' => ['type' => 'single', 'name' => 'Internet Connection Type', 'values' => self::INTERNET_TYPE],
        'bathroom' => ['type' => 'single', 'name' => 'Bathroom', 'values' => self::BATHROOM],
        'kitchenette' => ['type' => 'single', 'name' => 'Kitchen', 'values' => self::KITCHENETTE],
        'consumables' => ['type' => 'single', 'name' => 'Consumables', 'values' => self::CONSUMABLES],
        'bed_size' => ['type' => 'single', 'name' => 'Bed', 'values' => self::BED_SIZE],
        'features' => ['type' => 'multiple', 'name' => 'Features', 'values' => self::FEATURES],
        'accepting' => ['type' => 'multiple', 'name' => 'Accepting', 'values' => self::ACCEPTING],
        'consumables_included' => ['type' => 'multiple', 'name' => 'Consumables Included', 'values' => self::CONSUMABLES_INCLUDED],
        'services' => ['type' => 'single', 'name' => 'Services', 'values' => self::SERVICES],
        'services_included' => ['type' => 'multiple', 'name' => 'Services Included', 'values' => self::SERVICES_INCLUDED],
    ];

    public $timestamps = true;

    protected $fillable = [
        'rent_amount',
        'date_available',
        'date_available_text',
        'size',
        'title',
        'our_story',
        'internet_speed',
        'internet_unlimited',
        'is_services_included',
        'consumablesIncluded',
        'consumablesValue',
        'water',
        'electricity',
        'gas',
        'videoYoutube',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rights()
    {
        return $this->hasMany(RoomRight::class, 'room_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media()
    {
        return $this->hasMany(RoomMedia::class, 'room_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'room_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property()
    {
        return $this->hasOne(Property::class, 'id', 'property_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|User
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }
}
