<?php

namespace App\Repositories;

class MessageRepository extends Repository
{
    public static function getChatMessageData($messageId)
    {
        $message = \DB::table('messages as m')
            ->select(
                'm.*',
                'u.first_name',
                'u.last_name'
            )
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->where(['m.id' => $messageId])
            ->first();

        return $message;
    }
}
