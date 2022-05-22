<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;

class ChatPolicy extends Policy
{
    public function assignRoom(User $user, Chat $chat, Room $room)
    {
        $users = $chat->users()->get()->pluck('id')->toArray();
        /** @var Property $property */
        $property = $room->property()->first();

        return in_array($user->id, $users) && $property->creator_id === $user->id;
    }
}
