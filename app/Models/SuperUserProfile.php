<?php

namespace App\Models;

use App\Repositories\SuperUserProfileRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuperUserProfile
 *
 * @property int $id
 * @property int $status
 * @property string $avatar
 * @property string $phone
 * @property string $company_name
 * @property string $company_address
 * @property string $company_logo
 * @property string $twitter
 * @property string $facebook
 * @property string $instagram
 * @property string $youtube
 * @property string $created_at
 * @property string $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereYoutube($value)
 * @mixin \Eloquent
 * @property string|null $stripe_account_id
 * @property string|null $stripe_state
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereStripeAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SuperUserProfile whereStripeState($value)
 */
class SuperUserProfile extends Model
{
    protected $table = 'super_user_profile';

    const STATUS_CREATED = 1;
    const STATUS_FILLED = 2;

    protected $fillable = [
        'status',
        'phone',
        'twitter',
        'facebook',
        'instagram',
        'youtube',
        'company_name',
        'company_address',
        'company_logo',
    ];

    public function resolveRouteBinding($value)
    {
        return SuperUserProfileRepository::getById($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
