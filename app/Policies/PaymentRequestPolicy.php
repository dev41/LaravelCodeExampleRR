<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\PaymentRequest;
use App\Models\User;

class PaymentRequestPolicy extends Policy
{
    public function create(User $user): bool
    {
        return $user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER);
    }

    public function update(User $user, PaymentRequest $paymentRequest): bool
    {
        return $user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER) && $paymentRequest->created_by === $user->id;
    }

    public function viewRsPaymentRequest(User $user, PaymentRequest $paymentRequest): bool
    {
        $searchers = $paymentRequest->searchers()->get()->toArray();
        $spIds = array_column($searchers, 'rs_profile_id');

        return $user->hasAccount(AccountType::ACCOUNT_TYPE_SEARCHER) && in_array($user->id, $spIds);
    }
}
