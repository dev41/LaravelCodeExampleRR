<?php

namespace App\Services;

use App\Models\OnboardingPaymentPurchase;
use App\Models\OnboardingPaymentRequest;
use Illuminate\Support\Facades\DB;
use Stripe\Event;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class StripeWebhookService extends Service
{
    public function processEvent(Event $event)
    {
        switch ($event->type) {

            case Event::PAYMENT_INTENT_SUCCEEDED:

                $this->paymentIntentSuccess($event);

                break;
        }
    }

    public function paymentIntentSuccess(Event $event)
    {
        try {
            DB::beginTransaction();

            $clientSecret = $event->data->object->client_secret ?? null;

            if (!$clientSecret) {
                throw new InvalidParameterException('Client secret not found.');
            }

            /** @var OnboardingPaymentPurchase $purchase */
            $purchase = OnboardingPaymentPurchase::where([
                'pi_client_secret' => $clientSecret
            ])->first();

            if (!$purchase) {
                throw new InvalidParameterException('Purchase not found.');
            }

            $paymentRequest = OnboardingPaymentRequest::find($purchase->request_id);

            if (!$paymentRequest->isPayable()) {
                throw new InvalidParameterException('Payment request not payable.');
            }

            $purchase->status = OnboardingPaymentPurchase::STATUS_PAID;
            $purchase->save();

            $purchaseTypes = $paymentRequest->purchases()->where([
                'status' => OnboardingPaymentPurchase::STATUS_PAID,
            ])->pluck('type')->all();

            if (count($purchaseTypes) === 2 || in_array(OnboardingPaymentPurchase::TYPE_RENT_BOND, $purchaseTypes)) {
                $paymentRequest->status = OnboardingPaymentRequest::STATUS_PAID_BOND_RENT;
            } elseif (in_array(OnboardingPaymentPurchase::TYPE_BOND, $purchaseTypes)) {
                $paymentRequest->status = OnboardingPaymentRequest::STATUS_PAID_BOND;
            } elseif (in_array(OnboardingPaymentPurchase::TYPE_RENT, $purchaseTypes)) {
                $paymentRequest->status = OnboardingPaymentRequest::STATUS_PAID_RENT;
            }

            $paymentRequest->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function debugEvent(Event $event)
    {
        $debugText = str_repeat('-', 80) . PHP_EOL;
        $debugText .= 'TIME: ' . date('H:i:s', time() + 3*60*60) . PHP_EOL;
        $debugText .= 'TYPE: ' .  $event->type . PHP_EOL . PHP_EOL;
        $debugText .= json_encode($event, JSON_PRETTY_PRINT) . PHP_EOL;
        $debugText .= str_repeat('-', 80) . PHP_EOL . PHP_EOL;

        file_put_contents(base_path() . '/storage/webhook_log.txt', $debugText, FILE_APPEND);
    }
}
