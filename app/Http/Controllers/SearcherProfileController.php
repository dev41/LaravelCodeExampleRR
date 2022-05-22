<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearcherProfile\UpdateRequest;
use App\Libraries\Cache;
use App\Models\SearcherProfile;
use App\Services\SearcherProfileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileController
 * @package App\Http\Controllers
 */
class SearcherProfileController extends Controller
{
    const CACHE_KEY = 'api.searcher.profile';

    public function getById(int $userId, SearcherProfileService $profileService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $userId;
        $profile = Cache::getSet($key, function() use ($profileService, $userId) {
            return $profileService->getById($userId);
        });

        return response()->json($profile, Response::HTTP_OK);
    }

    public function update(SearcherProfile $profile, UpdateRequest $request, SearcherProfileService $profileService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $profile->id;
        $profileService->update($profile, $request->toArray());
        Cache::delete($key);

        return response()->json($profileService->getById($profile->id), Response::HTTP_OK);
    }
}
