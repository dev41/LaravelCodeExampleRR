<?php

namespace App\Models;

/**
 * Class PersonalInfoRequest
 * @package App\Models
 *
 * @property int $id
 * @property int $creator_id
 * @property int $searcher_id
 * @property int $chat_id
 * @property int $status
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property int $birthday
 * @property string $email
 * @property string $phone
 * @property string $emergency_name
 * @property string $emergency_phone
 * @property string $id_number
 * @property int $id_type
 * @property int $country
 * @property string $address
 * @property int $created_at
 * @property int $updated_at
 *
 */
class PersonalInfoRequest extends BaseModel
{
    protected $table = 'personal_info_request';

    const STATUS_CREATED = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_DECLINED = 2;
    const STATUS_INACTIVE = 3;

    const ID_TYPE_PASSPORT = 0;
    const ID_TYPE_DRIVER_LICENCE = 1;

    const COUNTRY_AUSTRALIA = 0;
    const COUNTRY_UK = 1;
    const COUNTRY_USA = 2;
    const COUNTRY_CANADA = 3;
    const COUNTRY_NEW_ZELAND = 4;

    protected $fillable = [
        'chat_id',
        'address',
        'birthday',
        'country',
        'email',
        'first_name',
        'id_number',
        'id_type',
        'last_name',
        'middle_name',
        'phone',
        'emergency_name',
        'emergency_phone',
    ];
}
