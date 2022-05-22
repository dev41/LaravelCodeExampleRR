<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Stripe\Product;
use Stripe\Stripe;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PlanService extends Service
{
    public function getPlanByStripeId(string $planId): Plan
    {
        if (!in_array($planId, Plan::PLAN_STRIPE_IDS)) {
            throw new InvalidParameterException('Stripe plan ID[' . $planId . '] not found.');
        }

        $plan = Plan::where(['stripe_id' => $planId])->first();

        if ($plan) {
            return $plan;
        }

        try {
            DB::beginTransaction();

            $plan = new Plan();
            $plan->stripe_id = $planId;
            $plan->name = Plan::PLAN_PARAMS[$planId]['name'];

            if ($planId !== Plan::PLAN_FREE) {
                $stripePlan = $this->createStripePlan($planId);
                $plan->amount = Plan::PLAN_PARAMS[$planId]['amount'];
            }

            $plan->save();
            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            throw new $e;
        }

        return $plan;
    }

    public function createStripePlan(string $planId): \Stripe\Plan
    {
        if (!in_array($planId, Plan::PLAN_STRIPE_IDS)) {
            throw new InvalidParameterException('Stripe plan ID[' . $planId . '] not found.');
        }

        Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));

        $product = $this->getProduct(Plan::PLAN_PARAMS[$planId]['product_name']);

        try {
            $plan = \Stripe\Plan::retrieve($planId);
        } catch (\Exception $e) {
            $plan = \Stripe\Plan::create([
                'id' => $planId,
                'amount' => Plan::PLAN_PARAMS[$planId]['amount'],
                'currency' => StripeService::CURRENCY,
                'interval' => Plan::PLAN_PARAMS[$planId]['interval'],
                'product' => ['name' => $product->name],
            ]);
        }

        return $plan;
    }

    public static function getProduct(string $productName): Product
    {
        try {
            /** @var Product $product */
            $product = Product::retrieve($productName);

        } catch (\Exception $e) {

            $productData = [
                'id' => $productName,
                'name' => $productName,
                'type' => 'service',
            ];
            $product = Product::create($productData);
        }

        return $product;
    }

}
