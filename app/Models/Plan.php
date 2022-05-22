<?php

namespace App\Models;

/**
 * Class Plan
 * @package App\Models
 *
 * @property int $id
 * @property string $stripe_id
 * @property string $name
 * @property string $product_name
 * @property int $amount
 */
class Plan extends BaseModel
{
    protected $table = 'plan';

    public $timestamps = false;

    const DEFAULT_AVAILABLE_ROOM_COUNT = 3;

    const PLAN_FREE_ID = 1;
    const PLAN_STANDARD_ID = 2;

    const PLAN_FREE = 'free';
    const PlAN_STANDARD = 'standard';

    const PLAN_STRIPE_IDS = [
        self::PLAN_FREE_ID => self::PLAN_FREE,
        self::PLAN_STANDARD_ID => self::PlAN_STANDARD,
    ];

    const PLAN_PARAMS = [
        self::PLAN_FREE => [
            'name' => 'Free',
        ],
        self::PlAN_STANDARD => [
            'name' => 'Standard',
            'amount' => '330',
            'interval' => 'month',
            'product_name' => 'standard',
        ],
    ];

    const PERMISSIONS_ADD_EVENT = 1;

    const PLAN_DETAILS = [

        self::PLAN_FREE => [
            'permissions' => [
                self::PERMISSIONS_ADD_EVENT,
            ],
        ],

        self::PlAN_STANDARD => [
            'permissions' => [

            ],
        ],
    ];
}
