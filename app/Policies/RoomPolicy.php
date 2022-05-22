<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomMedia;
use App\Models\User;

/**
 * Class RoomPolicy
 * @package App\Policies
 */
class RoomPolicy extends Policy
{
    /**
     * @param User $user
     * @return bool
     */
    public function before(User $user)
    {
        if (!$user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER)) {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Property $property
     * @return bool
     */
    public function create(User $user, Property $property)
    {
        $roleId = $property->property_rights()->where('user_id', $user->id)->get()->pluck('role_id')->first();
        return !empty($roleId) && in_array('room_create', self::RULES[$roleId]);
    }

    public function delete(User $user, Room $room)
    {
        $roleId = $room->rights()->where('user_id', $user->id)->get()->pluck('role_id')->first();
        return !empty($roleId) && in_array('room_delete', self::RULES[$roleId]);
    }

    /**
     * @param User $user
     * @param Room $room
     * @return bool
     */
    public function update(User $user, Room $room)
    {
        return $this->check($user, $room, 'room_update');
    }

    /**
     * @param User $user
     * @param Room $room
     * @return bool
     */
    public function createMedia(User $user, Room $room)
    {
        return $this->check($user, $room, 'create_room_media');
    }

    /**
     * @param User $user
     * @param Room $room
     * @return bool
     */
    public function updateMedia(User $user, Room $room)
    {
        return $this->check($user, $room, 'update_room_media');
    }

    public function deleteMedia(User $user, RoomMedia $media): bool
    {
        /** @var Room $room */
        $room = $media->room()->first();
        return $this->check($user, $room, 'delete_room_media');
    }

    /**
     * @param User $user
     * @param Room $room
     * @param string $rightName
     * @return bool
     */
    private function check(User $user, Room $room, string $rightName)
    {
        $roleId = $room->rights()->where('user_id', $user->id)->get()->pluck('role_id')->first();
        return !empty($roleId) && in_array($rightName, self::RULES[$roleId]);
    }
}
