<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentRequestPrice
 *
 * @package App\Models
 * @property int $id
 * @property int $payment_request_id
 * @property int $type
 * @property int $price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice wherePaymentRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestPrice whereType($value)
 * @mixin \Eloquent
 */
class PaymentRequestPrice extends Model
{
    protected $table = 'payment_request_price';
    public $timestamps = false;
}
