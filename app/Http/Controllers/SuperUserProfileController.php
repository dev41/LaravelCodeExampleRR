<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuperUserProfile\UpdateRequest;
use App\Libraries\Cache;
use App\Models\SuperUserProfile;
use App\Services\SuperUserProfileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SuperUserProfileController extends Controller
{
    const CACHE_KEY = 'api.super-user.profile';

    public function getById(int $userId, SuperUserProfileService $profileService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $userId;
        $profile = Cache::getSet($key, function() use ($profileService, $userId) {
            return $profileService->getById($userId);
        });

        return response()->json($profile, Response::HTTP_OK);
    }

    public function update(SuperUserProfile $profile, UpdateRequest $request, SuperUserProfileService $profileService)
    {
        $key = self::CACHE_KEY . '.id.' . $profile->id;
        $profileService->update($profile, $request->toArray());
        Cache::delete($key);

        return response()->json($profileService->getById($profile->id), Response::HTTP_OK);
    }

    public function uploadAvatar(SuperUserProfile $profile, SuperUserProfileService $profileService)
    {
        $imagePath = $profileService->uploadAvatar($profile);

        return response()->json('/' . $imagePath, Response::HTTP_OK);
    }

    public function uploadCompanyLogo(SuperUserProfile $profile, SuperUserProfileService $profileService)
    {
        $imagePath = $profileService->uploadCompanyLogo($profile);

        return response()->json('/' . $imagePath, Response::HTTP_OK);
    }
}
