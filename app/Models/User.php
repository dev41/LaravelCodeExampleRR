<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @package App\Models
 * @property int $id
 * @property string $stripe_customer_id
 * @property int $status
 * @property int $login_attempts
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property integer $gender
 * @property integer $age
 * @property string $phone_number
 * @property int $last_account_type
 * @mixin \Eloquent
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $online
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccountType[] $account_types
 * @property-read int|null $account_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLoginAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @property-read \App\Models\CalendarSettings $calendarSettings
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const STATUS_REQUESTED = -1;
    const STATUS_ACTIVE = 1;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDERS = [
        self::GENDER_MALE => 'Male',
        self::GENDER_FEMALE => 'Female',
    ];

    const ONLINE_NO = 1;
    const ONLINE_YES = 2;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'age',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'account_types'
    ];

    public function account_types()
    {
        return $this->hasMany(AccountType::class, 'user_id', 'id');
    }

    public function getAccountTypesAttribute()
    {
        $types = $this->account_types()->get()->toArray();
        unset($this->account_types);

        return array_map(function ($item) {
            return AccountType::ACCOUNT_TYPES[$item['account_type']];
        }, $types);
    }

    public function getLastAccountTypeAttribute($value)
    {
        return empty($value) ? null : AccountType::ACCOUNT_TYPES[$value];
    }

    public function isSuperUser(): bool
    {
        return $this->getOriginal('last_account_type') == AccountType::ACCOUNT_TYPE_SUPERUSER;
    }

    public function hasAccount(int $accountType): bool
    {
        $accountTypes = $this->account_types()->get()->pluck('account_type')->all();
        return in_array($accountType, $accountTypes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function calendarSettings()
    {
        return $this->hasOne(CalendarSettings::class, 'id', 'id');
    }
}
