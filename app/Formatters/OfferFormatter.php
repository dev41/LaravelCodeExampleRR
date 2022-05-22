<?php

namespace App\Formatters;

use App\Models\Offer;
use App\Models\Room;

class OfferFormatter extends Formatter
{
    public static function responseObject($offerData): array
    {
        $expireShift = Offer::EXPIRES_IN_HOURS[$offerData->expires_in] * 60;
        $expireTime = $expireShift - ((time() - strtotime($offerData->created_at)) / 60);
        $expireTime = ((int) ($expireTime / 60)) . ':' . ((int) ($expireTime % 60));

        $rentPeriod = null;
        if ($offerData->rent_period) {
            $rentPeriod = explode('|', $offerData->rent_period);
            $rentPeriod = reset($rentPeriod);

            $rentPeriod = Room::RENT_LABELS[$rentPeriod];
        }

        return [
            'id' => $offerData->id,
            'rent' => $offerData->rent_amount,
            'rentPeriod' => $rentPeriod,
            'bond' => $offerData->bond,
            'billsAmount' => $offerData->bills_amount,
            'billsIncluded' => $offerData->bills_included,
            'expirationDate' => $offerData->expires_in,
            'moveDate' => $offerData->move_in_date,
            'leaseLength' => $offerData->length_of_lease,

            'messageData' => [
                'id' => $offerData->id,
                'expiry' => $expireTime,
                'moveIn' => $offerData->move_in_date ? date('l, d M, Y', strtotime($offerData->move_in_date)) : null,
                'rent' => $offerData->rent_amount,
                'bond' => $offerData->bond,
                'lease' => Offer::LEASE_MONTH[$offerData->length_of_lease] ?? null,
            ],
        ];
    }
}
