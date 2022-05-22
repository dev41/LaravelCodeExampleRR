<?php

namespace App\Services;

use App\Http\Requests\OnboardingPaymentRequest\PayRequest;
use App\Http\Requests\OnboardingPaymentRequest\UpdateRequest;
use App\Models\Offer;
use App\Models\OnboardingPaymentPurchase;
use App\Models\OnboardingPaymentRequest;
use App\Models\SuperUserProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OnboardingPaymentRequestService extends Service
{
    public function createTmp(User $searcher): OnboardingPaymentRequest
    {
        /** @var Offer $offer */
        $offer = Offer::where([
                'searcher_id' => $searcher->id,
                'creator_id' => auth()->id(),
                'status' => Offer::STATUS_ACCEPTED,
            ])
            ->orderBy('created_at', 'desc')
            ->first();

        $request = new OnboardingPaymentRequest();
        $request->creator_id = auth()->id();
        $request->searcher_id = $searcher->id;
        $request->bond = $offer->bond;
        $request->rent = $offer->rent_amount;
        $request->save();

        return $request;
    }

    public function update(OnboardingPaymentRequest $request, UpdateRequest $updateRequest): string
    {
        DB::beginTransaction();

        try {
            $request->fill(array_filter($updateRequest->toArray()));
            $request->save();

            $purchase = new OnboardingPaymentPurchase();

            switch ($updateRequest->purpose) {

                case OnboardingPaymentRequest::PURPOSE_RENT:
                    $purchase->request_id = $request->id;
                    $purchase->type = OnboardingPaymentPurchase::TYPE_RENT;
                    $purchase->amount = $updateRequest->rent;
                    break;

                case OnboardingPaymentRequest::PURPOSE_BOND:
                    $purchase->request_id = $request->id;
                    $purchase->type = OnboardingPaymentPurchase::TYPE_BOND;
                    $purchase->amount = $updateRequest->bond;
                    break;

                case OnboardingPaymentRequest::PURPOSE_RENT_BOND:
                    $purchase->request_id = $request->id;
                    $purchase->type = OnboardingPaymentPurchase::TYPE_RENT_BOND;
                    $purchase->amount = $updateRequest->rent + $updateRequest->bond;
                    break;

                default: throw new InvalidParameterException('Purpose out of range.');
            }

            $suProfile = SuperUserProfile::find($request->creator_id);

            Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));
            $paymentIntent = PaymentIntent::create([
                'amount' => $purchase->amount * 100,
                'currency' => 'AUD',
                'application_fee_amount' => 10,
                'transfer_data' => [
                    'destination' => $suProfile->stripe_account_id,
                ],
                'payment_method_types' => ['au_becs_debit'],
            ]);
            $purchase->payment_intent_id = $paymentIntent->id;
            $purchase->pi_client_secret = $paymentIntent->client_secret;

            $purchase->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $paymentIntent->client_secret;
    }

    public function pay(OnboardingPaymentRequest $request, PayRequest $payRequest): OnboardingPaymentRequest
    {
        if (!$request->isPayable()) {
            throw new InvalidParameterException('Request cannot be payed.');
        }

        try {
            DB::beginTransaction();

            /** @var PaymentRequestService $paymentService */
            $paymentService = resolve(PaymentRequestService::class);

            Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));

            /** @var SuperUserProfile $searcherProfile */
            $suProfile = SuperUserProfile::find($request->creator_id);

            $chargeAppFee = $paymentService->getApplicationAmountFee($request->bond + $request->rent);
            $chargeDesc = $this->getPayDescription($request);

            $unpaidPurchases = $request->purchases()->where([
                'status' => OnboardingPaymentPurchase::STATUS_UNPAID,
            ])->get()->all();

            if (!$unpaidPurchases) {
                throw new InvalidParameterException('Request cannot be payed (unpaid purchases not found).');
            }

            $amounts = array_column($unpaidPurchases, 'amount');

            $charge = Charge::create([
                'amount' => array_sum($amounts) * 100,
                'currency' => StripeService::CURRENCY,
                'source' => $payRequest->token,
                'application_fee_amount' => $chargeAppFee,
                'destination' => $suProfile->stripe_account_id,
                'description' => $chargeDesc,
            ]);

            /** @var OnboardingPaymentPurchase $purchase */
            foreach ($unpaidPurchases as $purchase) {
                $purchase->status = OnboardingPaymentPurchase::STATUS_PAID;
                $purchase->save();
            }

            $purchaseTypes = $request->purchases()->where([
                'status' => OnboardingPaymentPurchase::STATUS_PAID,
            ])->pluck('type')->all();

            if (count($purchaseTypes) === 2 || in_array(OnboardingPaymentPurchase::TYPE_RENT_BOND, $purchaseTypes)) {
                $request->status = OnboardingPaymentRequest::STATUS_PAID_BOND_RENT;
            } elseif (in_array(OnboardingPaymentPurchase::TYPE_BOND, $purchaseTypes)) {
                $request->status = OnboardingPaymentRequest::STATUS_PAID_BOND;
            } elseif (in_array(OnboardingPaymentPurchase::TYPE_RENT, $purchaseTypes)) {
                $request->status = OnboardingPaymentRequest::STATUS_PAID_RENT;
            }

            $request->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $request;
    }

    public function getPayDescription(OnboardingPaymentRequest $request): string
    {
        /** @var User $superuser */
        $superuser = User::find($request->creator_id);
        /** @var User $superuser */
        $searcher = User::find($request->searcher_id);

        $amountDesc = [];
        if ($request->rent) {
            $amountDesc[] = 'rent $' . $request->rent;
        }
        if ($request->bond) {
            $amountDesc[] = 'bond $' . $request->bond;
        }

        $desc = 'Destination: onboarding payment request. SU name: (' . $superuser->id . ') ' .
            $superuser->first_name . ' ' . $superuser->last_name . '. ' .
            'Searcher name: (' . $searcher->id . ') ' . $searcher->first_name . ' ' . $searcher->last_name . '. ' .
            'Amount: ' . implode(', ', $amountDesc) . '.'
        ;

        return $desc;
    }

    public function decline(OnboardingPaymentRequest $request): OnboardingPaymentRequest
    {
        if ($request->status !== OnboardingPaymentRequest::STATUS_CREATED) {
            return $request;
        }
        $request->status = OnboardingPaymentRequest::STATUS_DECLINED;
        $request->save();

        return $request;
    }
}
