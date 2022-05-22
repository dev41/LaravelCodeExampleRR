<?php

namespace App\Formatters;

use App\Models\OnboardingPaymentPurchase;
use App\Models\OnboardingPaymentRequest;

class OnboardingPaymentRequestFormatter extends Formatter
{
    public static function responseObject($data): array
    {
        $purpose = null;
        if ($data->purchase) {
            switch ($data->purchase->type) {
                case OnboardingPaymentPurchase::TYPE_RENT:
                    $purpose = OnboardingPaymentRequest::PURPOSE_RENT;
                    break;
                case OnboardingPaymentPurchase::TYPE_BOND:
                    $purpose = OnboardingPaymentRequest::PURPOSE_BOND;
                    break;
                case OnboardingPaymentPurchase::TYPE_RENT_BOND:
                    $purpose = OnboardingPaymentRequest::PURPOSE_RENT_BOND;
                    break;
            }
        }

        $response = [
            'id' => $data->id,
            'expirationDate' => $data->expire_in,
            'moveDate' => $data->move_date,
            'rent' => $data->rent,
            'bond' => $data->bond,
            'status' => $data->status,
            'agreement' => $data->agreement,
            'purpose' => $purpose,
            'clientSecret' => $data->purchase ? $data->purchase->pi_client_secret : null,
        ];

        return $response;
    }
}
