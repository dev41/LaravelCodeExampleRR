<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository extends Repository
{
    public static function getById($userId)
    {
        $user = DB::table('users')->select([
            'id',
            'email',
            'phone_number as phone',
            'first_name',
            'last_name',
            'gender',
            'age',
        ])->where([
            'id' => $userId,
        ])->first();

        $chatsUserIds = DB::select(
            'SELECT (SELECT count(*) FROM user_chat _uc_count WHERE _uc_count.chat_id = c.id) AS users_count,
                   (SELECT group_concat(_uc_users.user_id) FROM user_chat _uc_users WHERE _uc_users.chat_id = c.id) as user_ids,
                   c.name
            FROM chat c
                     INNER JOIN user_chat uc ON c.id = uc.chat_id AND uc.user_id = ?
            GROUP BY c.id
            HAVING users_count = 2;
            ', [$userId]);

        if ($chatsUserIds) {
            $user->chatUserIds = implode(',', array_column($chatsUserIds, 'user_ids'));
        } else {
            $user->chatUserIds = '';
        }

        return ['el' => (array) $user];
    }

}
