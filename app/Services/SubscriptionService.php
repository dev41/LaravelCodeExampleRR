<?php

namespace App\Services;

use App\Formatters\UserSubscriptionInfoFormatter;
use App\Models\SubscriptionPurchase;
use App\Models\User;
use App\Repositories\SubscriptionRepository;
use Stripe\Card;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class SubscriptionService extends Service
{
    public function createCustomer(User $user): Customer
    {
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'description' => 'ID:' . $user->id,
        ]);

        return $customer;
    }

    public function subscribe(User $user, array $params): \App\Models\Subscription
    {
        Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));

        if (!$user->stripe_customer_id) {
            $customer = $this->createCustomer($user);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        $this->changeCard($user, $params['stripe_token']);

        /** @var PlanService $planService */
        $planService = resolve(PlanService::class);
        $plan = $planService->getPlanByStripeId($params['plan_id']);

        $subscriptionData = [
            'customer' => $user->stripe_customer_id,
            'items' => [
                [
                    'plan' => $plan->stripe_id,
                    'quantity' => (int) $params['room_count'],
                ],
            ],
            'cancel_at_period_end' => false,
        ];

        $stripeSubscription = Subscription::create($subscriptionData);

        $subscription = new \App\Models\Subscription();
        $subscription->stripe_id = $stripeSubscription->id;
        $subscription->plan_id = $plan->id;
        $subscription->user_id = $user->id;
        $subscription->status = \App\Models\Subscription::STATUS_ACTIVE;
        $subscription->room_count = $params['room_count'];
        $subscription->place_for = $params['place_for'];
        $subscription->save();

        if ($stripeSubscription->latest_invoice) {
            $invoice = Invoice::retrieve($stripeSubscription->latest_invoice);

            $subscriptionPurchase = new SubscriptionPurchase();
            $subscriptionPurchase->subscription_id = $subscription->id;
            $subscriptionPurchase->amount = $plan->amount * $subscription->room_count;
            $subscriptionPurchase->status = SubscriptionPurchase::STATUS_PAID;
            $subscriptionPurchase->details = $invoice->toJSON();
            $subscriptionPurchase->stripe_id = $invoice->id;
            $subscriptionPurchase->save();
        }

        return $subscription;
    }

    public function changeCard(User $user, string $token)
    {
        if (!$user->stripe_customer_id) {
            return false;
        }

        /** @var Customer $customer */
        $customer = Customer::retrieve($user->stripe_customer_id);

        if (!$customer) {
            throw new InvalidParameterException('Stripe customer not found.');
        }

        /*
         * Stripe API: https://stripe.com/docs/api/cards/delete?lang=php
         * If you delete a card that is currently the default source,
         * then the most recently added source will become the new default.
         */

        /** @var Card $source */
        foreach ($customer->sources->data as $source) {
            Customer::deleteSource($user->stripe_customer_id, $source->id);
        }

        $customer->sources->create([
            'source' => $token,
        ]);
        $customer->save();

        return true;
    }

    public function getUserInfo(int $userId): array
    {
        $info = SubscriptionRepository::getActiveSubscriptionInfo($userId);
        return UserSubscriptionInfoFormatter::responseObject($info);
    }

}
