<?php

namespace App\Repositories;

use App\Models\OnboardingAgreement;
use App\Models\OnboardingPaymentPurchase;
use Illuminate\Support\Facades\DB;

class OnboardingPaymentRequestRepository extends Repository
{
    public static function getById(int $requestId)
    {
        $data = DB::table('onboarding_payment_request as opr')
            ->select(
                'opr.id',
                'opr.rent',
                'opr.bond',
                'opr.status',
                self::getSelectDateTimeColumn('opr.move_date'),
                self::getSelectDateTimeColumn('opr.expire_in')
            )
            ->where('id', '=', $requestId)
            ->first();

        $agreement = OnboardingAgreement::where([
            'request_id' => $requestId,
            'type' => OnboardingAgreement::TYPE_ORIGINAL,
        ])->first();

        $purchase = OnboardingPaymentPurchase::where([
            'request_id' => $requestId,
            'status' => OnboardingPaymentPurchase::STATUS_UNPAID,
        ])->first();

        $data->agreement = $agreement;
        $data->purchase = $purchase;

        return $data;
    }
}
