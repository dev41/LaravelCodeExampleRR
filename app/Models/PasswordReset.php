<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PasswordReset
 *
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset whereToken($value)
 * @mixin \Eloquent
 */
class PasswordReset extends Model
{
    const UPDATED_AT = null;
    const EXPIRE_TIME = 60 * 60 * 24;

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $fillable = [
        'email',
        'token',
    ];

    public function isValid(): bool
    {
        return (strtotime($this->created_at) + self::EXPIRE_TIME) > time();
    }
}
