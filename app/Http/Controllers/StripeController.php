<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\StripeWebhookService;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function connectAccount(StripeService $stripeService)
    {
        $state = request()->post('state');
        $code = request()->post('code');

        $stripeService->connectAccount($state, $code);

        return redirect(env('FE_URL'));
    }

    public function webhook(StripeWebhookService $stripeWebhookService)
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, env('STRIPE_WH_SIGNATURE_SECRET')
            );
        } catch(\UnexpectedValueException $e) {
            throw $e;
        } catch(SignatureVerificationException $e) {
            throw $e;
        }

        $stripeWebhookService->processEvent($event);

        return response()->json(['success' => true]);
    }
}
