<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends Repository
{
    public static function getActiveSubscriptionInfo(int $userId)
    {
        $data = DB::table('subscription as s')
            ->select([
                's.*',
                'p.stripe_id as plan_stripe_id',
            ])
            ->join('plan as p', 'p.id', '=', 's.plan_id')
            ->where([
                's.user_id' => $userId,
                's.status' => Subscription::STATUS_ACTIVE,
            ])
            ->orderBy('s.created_at', 'desc')
            ->first();

        return $data ? (array) $data : [];
    }
}
