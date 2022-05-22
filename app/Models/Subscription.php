<?php

namespace App\Models;

/**
 * Class Subscription
 * @package App\Models
 *
 * @property int $id
 * @property int $plan_id
 * @property int $user_id
 * @property string $stripe_id
 * @property int $status
 * @property int $place_for
 * @property int $room_count
 * @property int $created_at
 * @property int $updated_at
 *
 */
class Subscription extends BaseModel
{
    protected $table = 'subscription';

    const STATUS_CREATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const DEFAULT_ROOM_COUNT = 10;
}
