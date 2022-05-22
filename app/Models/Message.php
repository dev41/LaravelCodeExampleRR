<?php

namespace App\Models;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $user_id
 * @property int $chat_id
 * @property int $type
 * @property int $status
 * @property string $message
 * @property string $data
 * @property string $ip
 * @property int $file_format
 * @property string $file_path
 * @property string $created_at
 * @property int|null $client_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereClientToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereFileFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereData($value)
 */
class Message extends BaseModel
{
    protected $table = 'messages';

    const TYPE_TEXT = 1;

    const TYPE_PAYMENT_REQUEST_SEND = 2;
    const TYPE_PAYMENT_REQUEST_ACCEPTED = 3;
    const TYPE_PAYMENT_REQUEST_CANCELED = 4;
    const TYPE_PAYMENT_REQUEST_ACCEPT_CONFIRMATION = 5;
    const TYPE_PAYMENT_REQUEST_CANCELED_CONFIRMATION = 6;

    const TYPE_VIEWING_REQUEST_SEND = 20;
    const TYPE_VIEWING_REQUEST_ACCEPTED = 21;
    const TYPE_VIEWING_REQUEST_CANCELED = 22;

    const TYPE_VIEWING_SEARCHER_REQUEST_SEND = 23;
    const TYPE_VIEWING_SEARCHER_REQUEST_SU_ACCEPT = 24;
    const TYPE_VIEWING_SEARCHER_REQUEST_SU_CANCEL = 25;

    const TYPE_VIEWING_REQUEST_SU_ACCEPTED = 26;
    const TYPE_VIEWING_REQUEST_SU_CANCELED = 27;

    const TYPE_ASSIGN_ROOM_CONFIRMATION = 40;

    const TYPE_OFFER_SEND = 50;
    const TYPE_OFFER_ACCEPTED = 51;
    const TYPE_OFFER_DECLINED = 52;
    const TYPE_OFFER_EXPIRED = 53;
    const TYPE_OFFER_RS_HIDE = 57;
    const TYPE_OFFER_CLOSED = 58;

    const TYPE_PERSONAL_INFO_REQUEST = 60;
    const TYPE_PERSONAL_INFO_ACCEPTED = 61;
    const TYPE_PERSONAL_INFO_DECLINE = 62;
    const TYPE_PERSONAL_INFO_RS_HIDE = 67;
    const TYPE_PERSONAL_CLOSED = 68;

    const TYPE_ONBOARDING_PAYMENT_REQUEST = 70;
    const TYPE_ONBOARDING_PAYMENT_PAID_BOTH = 71;
    const TYPE_ONBOARDING_PAYMENT_PAID_BOND = 72;
    const TYPE_ONBOARDING_PAYMENT_PAID_RENT = 73;
    const TYPE_ONBOARDING_PAYMENT_PAID = 74;
    const TYPE_ONBOARDING_PAYMENT_DECLINE = 75;
    const TYPE_ONBOARDING_PAYMENT_RS_HIDE = 77;

    const TYPE_ONBOARDING_AGREEMENT_SU_REQUEST_SEND = 80;
    const TYPE_ONBOARDING_AGREEMENT_SU_REQUEST_HIDE = 81;
    const TYPE_ONBOARDING_AGREEMENT_RS_SINGED = 82;
    const TYPE_ONBOARDING_AGREEMENT_RS_DECLINED = 83;
    const TYPE_ONBOARDING_AGREEMENT_TERMINATED = 87;
    const TYPE_ONBOARDING_AGREEMENT_CLOSED = 88;

    const STATUS_SEND = 0;
    const STATUS_CREATED = 1;
    const STATUS_SEEN = 2;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'chat_id',
        'type',
        'status',
        'message',
        'ip',
        'file_format',
        'file_path',
        'created_at',
    ];
}
