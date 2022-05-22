<?php

namespace App\Models;

/**
 * App\Models\UserChat
 *
 * @property int $user_id
 * @property int $chat_id
 * @property int $user_role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserChat whereUserRole($value)
 * @mixin \Eloquent
 */
class UserChat extends BaseModel
{
    protected $table = 'user_chat';

    const USER_ROLE_READER = 1;
    const USER_ROLE_WRITER = 2;
    const USER_ROLE_OWNER = 3;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'chat_id',
        'user_role',
    ];
}
