<?php

namespace App\Models;

/**
 * App\Models\Chat
 *
 * @property int $id
 * @property string $name
 * @property int $room_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Chat whereRoomId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 */
class Chat extends BaseModel
{
    protected $table = 'chat';

    public $timestamps = false;

    const ONBOARDING_STEP_OFFER = 10;
    const ONBOARDING_STEP_OFFER_ACCEPTED = 11;
    const ONBOARDING_STEP_INFO_REQUESTED = 20;
    const ONBOARDING_STEP_INFO_ACCEPTED = 21;
    const ONBOARDING_STEP_AGREEMENT_REQUESTED = 30;
    const ONBOARDING_STEP_AGREEMENT_ACCEPTED = 31;
    const ONBOARDING_STEP_PAYMENT_BOTH = 40;
    const ONBOARDING_STEP_PAYMENT_BOND = 41;
    const ONBOARDING_STEP_PAYMENT_RENT = 42;
    const ONBOARDING_STEP_COMPLETE = 100;

    protected $fillable = [
        'name',
        'room_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chat');
    }
}
