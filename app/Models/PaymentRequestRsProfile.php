<?php

namespace App\Models;

use App\Libraries\THasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentRequestRsProfile
 *
 * @property int $payment_request_id
 * @property int $rs_profile_id
 * @property int $status
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string $id_number
 * @property string $birthday
 * @property string|null $stripe_card_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile wherePaymentRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereRsProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestRsProfile whereStripeCardToken($value)
 * @mixin \Eloquent
 */
class PaymentRequestRsProfile extends Model
{
    use THasCompositePrimaryKey;

    protected $table = 'payment_request_rs_profile';
    protected $primaryKey = ['payment_request_id', 'rs_profile_id'];
    public $timestamps = false;
    public $incrementing = false;


    const STATUS_SEND = 1;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'id_number',
        'birthday',
        'stripe_card_token',
    ];
}
