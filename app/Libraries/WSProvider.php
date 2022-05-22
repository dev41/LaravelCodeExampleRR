<?php

namespace App\Libraries;

class WSProvider
{
    public static function changeMessageType(int $chatId, int $messageId, int $type)
    {
        self::sendWSPost(__FUNCTION__, [
            'chatId' => $chatId,
            'messageId' => $messageId,
            'type' => $type,
        ]);
    }

    public static function changeChatState(int $chatId, array $chatData)
    {
        self::sendWSPost(__FUNCTION__, [
            'chatId' => $chatId,
            'chatData' => $chatData,
        ]);
    }

    public static function sendSpecialMessage(int $chatId, int $user_id, int $type, array $data = [])
    {
        $message = array_merge([
            'chat_id' => $chatId,
            'user_id' => $user_id,
            'type' => $type,
            'client_token' => null,
            'message' => '',
        ]);

        if (!empty($data)) {
            $message['data'] = $data;
        }

        self::sendWSPost(__FUNCTION__, [
            'message' => $message,
        ]);
    }

    public static function sendWSPost(string $method, array $data): string
    {
        $out = false;
        if ($curl = curl_init()) {
            $url = env('WS_URL') . $method;
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            $out = curl_exec($curl);
            curl_close($curl);
        }
        return $out;
    }
}
