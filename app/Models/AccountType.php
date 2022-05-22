<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountType
 *
 * @package App\Models
 * @property int $user_id
 * @property int $account_type
 * @property-read \App\Models\User $property
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountType whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountType whereUserId($value)
 * @mixin \Eloquent
 */
class AccountType extends Model
{
    const ACCOUNT_TYPE_SUPERUSER = 1;
    const ACCOUNT_TYPE_SEARCHER = 2;
    const ACCOUNT_TYPE_ADMIN = 3;

    const ACCOUNT_TYPES = [
        self::ACCOUNT_TYPE_SUPERUSER => 'superuser',
        self::ACCOUNT_TYPE_SEARCHER => 'searcher',
        self::ACCOUNT_TYPE_ADMIN => 'admin',
    ];

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'account_type'
    ];

    public function property()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
