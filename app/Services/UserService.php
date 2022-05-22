<?php

namespace App\Services;

use App\Models\AccountType;
use App\Models\SearcherProfile;
use App\Models\SuperUserProfile;
use App\Models\User;
use App\Repositories\SearcherProfileRepository;
use App\Repositories\SuperUserProfileRepository;
use Illuminate\Support\Str;
use Stripe\Account;
use Stripe\Stripe;

class UserService extends Service
{
    public function getUserLoginInfo(User $user): array
    {
        $user->profile_setup = $this->profileIsFilled($user);

        $authService = resolve(AuthService::class);
        $token = $authService->createToken($user);

        $subscriptionService = resolve(SubscriptionService::class);
        $subscriptionInfo = $subscriptionService->getUserInfo($user->id);

        $rsProfile = SearcherProfile::find($user->id);
        $suProfile = SuperUserProfile::find($user->id);

        $stripeConnectLink = null;
        $stripeConnectLoginLink = null;

        if ($suProfile) {
            if (!$suProfile->stripe_account_id) {
                $suProfile->stripe_state = Str::random(50);
                $suProfile->save();

                $stripeConnectLink = strtr('{connect_link}&state={state}&redirect_uri={redirect_uri}', [
                    '{connect_link}' => env('STRIPE_CONNECT_LINK'),
                    '{state}' => $suProfile->stripe_state,
//                '{redirect_uri}' => 'https://47d10c45.ngrok.io/api/stripe/connect',
                    '{redirect_uri}' => url('api/stripe/connect'),
                ]);
            } elseif ($suProfile->stripe_state) {

                $suProfile->stripe_state = null;
                $suProfile->save();
            }

            if ($suProfile->stripe_account_id) {
                Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));
                try {
                    $stripeConnectLoginLink = Account::createLoginLink($suProfile->stripe_account_id)->url;
                } catch (\Exception $e) {
                }
            }
        }

        return [
            'user' => $user,
            'token' => $token,
            'stripeConnectLink' => (string) $stripeConnectLink,
            'stripeConnectLoginLink' => (string) $stripeConnectLoginLink,
            'rsProfile' => $rsProfile,
            'suProfile' => $suProfile,
            'subscription' => $subscriptionInfo,
        ];
    }

    public function profileIsFilled(User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user->getOriginal('last_account_type') === AccountType::ACCOUNT_TYPE_SUPERUSER) {
            $superuserProfile = SuperUserProfileRepository::getById($user->id);
            return $superuserProfile && $superuserProfile->status === SuperUserProfile::STATUS_FILLED;
        } else {
            $searcherProfile = SearcherProfileRepository::getById($user->id);
            return $searcherProfile && $searcherProfile->status === SearcherProfile::STATUS_FILLED;
        }
    }
}
