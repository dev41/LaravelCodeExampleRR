<?php

namespace App\Formatters;

use App\Models\Plan;

class UserSubscriptionInfoFormatter extends Formatter
{
    public static function responseObject($data): array
    {
        return [
            'roomCount' => $data['room_count'] ?? Plan::DEFAULT_AVAILABLE_ROOM_COUNT,
            'planId' => $data['plan_stripe_id'] ?? Plan::PLAN_FREE,
        ];
    }
}
