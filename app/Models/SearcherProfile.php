<?php

namespace App\Models;

use App\Repositories\SearcherProfileRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SearchProfile
 *
 * @package App
 * @property int $id
 * @property string $stripe_id
 * @property int $status
 * @property string $avatar
 * @property string $story
 * @property string $video_youtube
 * @property int $rent_amount
 * @property int $rent
 * @property int $children
 * @property string $move_date
 * @property string $move_date_text
 * @property int $internet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereInternet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereMoveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereMoveDateText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereRent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherProfile whereVideoYoutube($value)
 * @mixin \Eloquent
 */
class SearcherProfile extends Model
{
    const STATUS_CREATED = 1;
    const STATUS_FILLED = 2;

    const PLACE_FOR_SINGLE = 1;
    const PLACE_FOR_COUPLE = 2;
    const PLACE_FOR_SHARING = 3;
    const PLACE_FOR = [
        self::PLACE_FOR_SINGLE => ['name' => 'Single', 'icon' => 'sign-up'],
        self::PLACE_FOR_COUPLE => ['name' => 'Couple', 'icon' => 'room-type'],
        self::PLACE_FOR_SHARING => ['name' => 'Sharing', 'icon' => 'flatmates'],
    ];

    const RENTAL_PERIOD_1MONTH = 1;
    const RENTAL_PERIOD_2MONTH = 2;
    const RENTAL_PERIOD_3MONTH = 3;
    const RENTAL_PERIOD_4MONTH = 4;
    const RENTAL_PERIOD_6MONTH = 6;
    const RENTAL_PERIOD_9MONTH = 9;
    const RENTAL_PERIOD_12MONTH = 12;
    const RENTAL_PERIOD = [
        self::RENTAL_PERIOD_1MONTH => ['name' => '1 month', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_2MONTH => ['name' => '2 months', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_3MONTH => ['name' => '3 months', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_4MONTH => ['name' => '4 months', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_6MONTH => ['name' => '6 months', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_9MONTH => ['name' => '9 months', 'icon' => 'calendar'],
        self::RENTAL_PERIOD_12MONTH => ['name' => '12+ months', 'icon' => 'calendar'],
    ];

    const RENT_WEEK = 1;
    const RENT_MONTH = 2;
    const RENT = [
        self::RENT_WEEK => 'week',
        self::RENT_MONTH => 'month',
    ];

    const OCCUPANCIES_ANYONE = 1;
    const OCCUPANCIES_FEMALE = 2;
    const OCCUPANCIES_MALE = 3;
    const OCCUPANCIES_COUPLE = 4;
    const OCCUPANCIES = [
        self::OCCUPANCIES_ANYONE => ['name' => 'Anyone', 'icon' => 'flatmates'],
        self::OCCUPANCIES_FEMALE => ['name' => 'Female', 'icon' => 'female'],
        self::OCCUPANCIES_MALE => ['name' => 'Male', 'icon' => 'male'],
        self::OCCUPANCIES_COUPLE => ['name' => 'Couple', 'icon' => 'room-type'],
    ];

    const EMPLOYMENT_STATUS_WORKING_FULL_TIME = 1;
    const EMPLOYMENT_STATUS_WORKING_PART_TIME = 2;
    const EMPLOYMENT_STATUS_WORKING_HOLIDAY = 3;
    const EMPLOYMENT_STATUS_RETIRED = 4;
    const EMPLOYMENT_STATUS_UNEMPLOYED = 5;
    const EMPLOYMENT_STATUS_BACKPACKER = 6;
    const EMPLOYMENT_STATUS_STUDENT_INTERNATIONAL = 7;
    const EMPLOYMENT_STATUS_STUDENT_LOCAL = 8;
    const EMPLOYMENT_STATUS_WORK_FROM_HOME = 9;
    const EMPLOYMENT_STATUS_SELF_EMPLOYED = 10;
    const EMPLOYMENT_STATUS_OTHER = 11;
    const EMPLOYMENT_STATUS = [
        self::EMPLOYMENT_STATUS_WORKING_FULL_TIME => ['name' => 'Working full time', 'icon' => '', 'tooltipRequired' => false],
        self::EMPLOYMENT_STATUS_WORKING_PART_TIME => ['name' => 'Working part-time', 'icon' => '', 'tooltipRequired' => false],
        self::EMPLOYMENT_STATUS_WORKING_HOLIDAY => ['name' => 'Working holiday', 'icon' => '', 'tooltipRequired' => false],
        self::EMPLOYMENT_STATUS_RETIRED => ['name' => 'Retired', 'icon' => '', 'tooltipRequired' => true],
        self::EMPLOYMENT_STATUS_UNEMPLOYED => ['name' => 'Unemployed', 'icon' => '', 'tooltipRequired' => true],
        self::EMPLOYMENT_STATUS_BACKPACKER => ['name' => 'Backpacker', 'icon' => '', 'tooltipRequired' => true],
        self::EMPLOYMENT_STATUS_STUDENT_INTERNATIONAL => ['name' => 'Student international', 'icon' => '', 'tooltipRequired' => true],
        self::EMPLOYMENT_STATUS_STUDENT_LOCAL => ['name' => 'Student local', 'icon' => '', 'tooltipRequired' => true],
        self::EMPLOYMENT_STATUS_WORK_FROM_HOME => ['name' => 'Work from home', 'icon' => '', 'tooltipRequired' => false],
        self::EMPLOYMENT_STATUS_SELF_EMPLOYED => ['name' => 'Self-employed', 'icon' => '', 'tooltipRequired' => false],
        self::EMPLOYMENT_STATUS_OTHER => ['name' => 'Other', 'icon' => '', 'tooltipRequired' => true],
    ];

    const FURNISHING_NOT_REQUIRED = 1;
    const FURNISHING_REQUIRED = 2;
    const FURNISHING_FLEXIBLE = 3;
    const FURNISHING = [
        self::FURNISHING_NOT_REQUIRED => ['name' => 'Not required', 'icon' => 'furnishings'],
        self::FURNISHING_REQUIRED => ['name' => 'Required', 'icon' => 'furnishings'],
        self::FURNISHING_FLEXIBLE => ['name' => 'Flexible', 'icon' => 'furnishings'],
    ];

    const PARKING_OFF_STREET_PARKING = 1;
    const PARKING_ON_STREET_PARKING = 2;
    const PARKING_NO_PARKING = 3;
    const PARKING_COVERED_PARKING = 4;
    const PARKING_RENTED_PARKING = 5;
    const PARKING_PERMIT_PARKING = 6;
    const PARKING = [
        self::PARKING_OFF_STREET_PARKING => ['name' => 'Off-street parking', 'icon' => 'parking'],
        self::PARKING_ON_STREET_PARKING => ['name' => 'On-street parking', 'icon' => 'parking'],
        self::PARKING_NO_PARKING => ['name' => 'No parking', 'icon' => 'parking'],
        self::PARKING_COVERED_PARKING => ['name' => 'Covered parking', 'icon' => 'parking'],
        self::PARKING_RENTED_PARKING => ['name' => 'Rented parking', 'icon' => 'parking'],
        self::PARKING_PERMIT_PARKING => ['name' => 'Permit parking', 'icon' => 'parking'],
    ];

    const BATHROOM_SHARED = 1;
    const BATHROOM_PRIVATE = 2;
    const BATHROOM = [
        self::BATHROOM_SHARED => ['name' => 'Shared', 'icon' => 'bathroom'],
        self::BATHROOM_PRIVATE => ['name' => 'Private', 'icon' => 'bathroom'],
    ];

    const KITCHEN_SHARED = 1;
    const KITCHEN_PRIVATE = 2;
    const KITCHEN = [
        self::KITCHEN_SHARED => ['name' => 'Shared', 'icon' => 'kitchenette'],
        self::KITCHEN_PRIVATE => ['name' => 'Private', 'icon' => 'kitchenette'],
    ];

    const LIFESTYLE_PET = 1;
    const LIFESTYLE_LGBT = 2;
    const LIFESTYLE_SMOKING = 3;
    const LIFESTYLE_MUSIC_LOVER = 4;
    const LIFESTYLE_SOCIAL_DRINK = 5;
    const LIFESTYLE_NON_ABLE_BODIED = 6;
    const LIFESTYLE_THE_HANDYMAN = 7;
    const LIFESTYLE_THE_SOCIAL_BUTTERFLY = 8;
    const LIFESTYLE_THE_PARTY_ANIMAL = 9;
    const LIFESTYLE_THE_SUPER_CLEAN_FLATMATE = 10;
    const LIFESTYLE_THE_DRAMA_FREE_FLATMATE = 11;
    const LIFESTYLE = [
        self::LIFESTYLE_PET => ['name' => 'Pet', 'icon' => 'pets'],
        self::LIFESTYLE_LGBT => ['name' => 'LGBT', 'icon' => 'lgbt'],
        self::LIFESTYLE_SMOKING => ['name' => 'Smoking', 'icon' => 'smoking'],
        self::LIFESTYLE_MUSIC_LOVER => ['name' => 'Music lover', 'icon' => 'music-lover'],
        self::LIFESTYLE_SOCIAL_DRINK => ['name' => 'Social drink', 'icon' => 'no-alcohol'],
        self::LIFESTYLE_NON_ABLE_BODIED => ['name' => 'Non-able-bodied', 'icon' => 'non-able-bodied'],
        self::LIFESTYLE_THE_HANDYMAN => ['name' => 'The handyman', 'icon' => 'toolbox'],
        self::LIFESTYLE_THE_SOCIAL_BUTTERFLY => ['name' => 'The social butterfly', 'icon' => 'share-button'],
        self::LIFESTYLE_THE_PARTY_ANIMAL => ['name' => 'The party animal', 'icon' => 'dog'],
        self::LIFESTYLE_THE_SUPER_CLEAN_FLATMATE => ['name' => 'The super clean flatmate', 'icon' => 'leaf-fresh'],
        self::LIFESTYLE_THE_DRAMA_FREE_FLATMATE => ['name' => 'The drama-free flatmate', 'icon' => 'hands-helping'],
    ];

    const INTERNET_FLEXIBLE = 1;
    const INTERNET_REQUIRED = 2;
    const INTERNET = [
        self::INTERNET_FLEXIBLE => 'Flexible',
        self::INTERNET_REQUIRED => 'Required',
    ];

    const INTERNET_OPTION_VIDEO_STREAMING = 1;
    const INTERNET_OPTION_HOME_BUSINESS = 2;
    const INTERNET_OPTION_BROWSING = 3;
    const INTERNET_OPTION_VIDEO_GAME = 4;
    const INTERNET_OPTION_SOCIAL_MEDIA = 5;
    const INTERNET_OPTION_OTHER = 6;
    const INTERNET_OPTION = [
        self::INTERNET_OPTION_VIDEO_STREAMING => ['name' => 'Video streaming', 'icon' => ''],
        self::INTERNET_OPTION_HOME_BUSINESS => ['name' => 'Home business', 'icon' => ''],
        self::INTERNET_OPTION_BROWSING => ['name' => 'Browsing', 'icon' => ''],
        self::INTERNET_OPTION_VIDEO_GAME => ['name' => 'Video game', 'icon' => ''],
        self::INTERNET_OPTION_SOCIAL_MEDIA => ['name' => 'Social media', 'icon' => ''],
        self::INTERNET_OPTION_OTHER => ['name' => 'Other', 'icon' => ''],
    ];

    const PREFERENCES_AIR_CONDITIONER = 1;
    const PREFERENCES_DESK = 2;
    const PREFERENCES_TV = 3;
    const PREFERENCES_DOOR_LOCK = 4;
    const PREFERENCES_EXTRA_STORAGE = 5;
    const PREFERENCES_PRIVATE_ACCESS = 6;
    const PREFERENCES_ENSUITES = 7;
    const PREFERENCES_HEATER = 8;
    const PREFERENCES = [
        self::PREFERENCES_AIR_CONDITIONER => ['name' => 'Air conditioner', 'icon' => 'air-conditioner'],
        self::PREFERENCES_DESK => ['name' => 'Desk', 'icon' => 'desk'],
        self::PREFERENCES_TV => ['name' => 'TV', 'icon' => 'tv'],
        self::PREFERENCES_DOOR_LOCK => ['name' => 'Door lock', 'icon' => 'door-lock'],
        self::PREFERENCES_EXTRA_STORAGE => ['name' => 'Extra storage', 'icon' => 'boxes'],
        self::PREFERENCES_PRIVATE_ACCESS => ['name' => 'Private access', 'icon' => 'key'],
        self::PREFERENCES_ENSUITES => ['name' => 'Ensuites', 'icon' => 'door'],
        self::PREFERENCES_HEATER => ['name' => 'Heater', 'icon' => 'heater'],
    ];

    const PARAMS = [
        'place_for' => ['type' => 'single', 'name' => 'PlaceFor', 'values' => self::PLACE_FOR],
        'rental_period' => ['type' => 'single', 'name' => 'Length of stay', 'values' => self::RENTAL_PERIOD],
        'occupancies' => ['type' => 'single', 'name' => 'Occupancies', 'values' => self::OCCUPANCIES],

        'employment_status' => ['type' => 'multiple', 'name' => 'Employment status', 'values' => self::EMPLOYMENT_STATUS],

        'furnishing' => ['type' => 'single', 'name' => 'Furnishing', 'values' => self::FURNISHING],
        'parking' => ['type' => 'single', 'name' => 'Parking', 'values' => self::PARKING],
        'bathroom' => ['type' => 'single', 'name' => 'Bathroom', 'values' => self::BATHROOM],
        'kitchen' => ['type' => 'single', 'name' => 'Kitchenette', 'values' => self::KITCHEN],

        'lifestyle' => ['type' => 'multiple', 'name' => 'Lifestyle', 'values' => self::LIFESTYLE],

        'internet_option' => ['type' => 'multiple', 'name' => 'Internet option', 'values' => self::INTERNET_OPTION],

        'preferences' => ['type' => 'multiple', 'name' => 'Preferences', 'values' => self::PREFERENCES],
    ];

    protected $fillable = [
        'story',
        'video_youtube',
        'rent_amount',
        'rent',
        'status',
        'children',
        'move_date',
        'move_date_text',
        'internet',
    ];

    public function resolveRouteBinding($value)
    {
        return SearcherProfileRepository::getById($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
