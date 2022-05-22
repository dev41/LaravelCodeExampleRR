<?php

namespace App\Formatters;

use App\Models\Room;

class OnboardingAgreementsFormatter extends Formatter
{
    public static function responseObject($agreementData): array
    {
        return [
            'id' => $agreementData->id,
            'chatId' => $agreementData->chat_id,
            'status' => $agreementData->status,
            'emergencyRepair' => [
                'name' => $agreementData->emergency_repair_name,
                'phone' => $agreementData->emergency_repair_phone,
            ],
            'offer' => [
                'rent' => $agreementData->rent,
                'rentPeriod' => $agreementData->rent_period ? Room::RENT_LABELS[$agreementData->rent_period] : '',
                'bond' => $agreementData->bond,
                'billsAmount' => $agreementData->bills_amount,
                'billsIncluded' => $agreementData->bills_included,
                'moveIn' => $agreementData->move_date,
                'agreementEndDate' => $agreementData->expire_in,
                'lease' => $agreementData->lease,
                'agreementLength' => $agreementData->lease,
                'fullName' => $agreementData->full_name,
            ],
            'suDetails' => json_decode($agreementData->su_details),
            'searcherDetails' => json_decode($agreementData->searcher_details),
            'docs' => [
                'agreement' => $agreementData->agreement,
                'houseRules' => $agreementData->houseRules,
            ],
        ];
    }
}
