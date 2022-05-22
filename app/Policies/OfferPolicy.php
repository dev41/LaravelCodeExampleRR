<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;

class OfferPolicy extends Policy
{
    public function createTmp(User $user, Room $room)
    {
        /** @var Property $property */
        $property = $room->property()->first();
        return $property->creator_id === $user->id;
   }

    public function update(User $user, Offer $offer)
    {
        return $offer->creator_id === $user->id;
    }

    public function rsUpdate(User $user, Offer $offer)
    {
        return $offer->searcher_id === $user->id;
    }
}
