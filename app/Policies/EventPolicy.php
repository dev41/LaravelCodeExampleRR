<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy extends Policy
{
    public function delete(User $user, Event $event)
    {
        return $event->creator_id === $user->id;
    }
}
