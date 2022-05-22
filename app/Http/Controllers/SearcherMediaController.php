<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\CreateMediaRequest;
use App\Models\SearcherMedia;
use App\Models\SearcherProfile;
use App\Repositories\SearcherMediaFileRepository;
use App\Repositories\SearcherMediaRepository;
use App\Services\SearcherMediaService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SearcherMediaController extends Controller
{
    const CACHE_KEY = 'api.searcher.media';

    public function create(SearcherProfile $profile, CreateMediaRequest $request, SearcherMediaService $mediaService): JsonResponse
    {
        $media = $mediaService->create($profile, $request->type);

        $images = SearcherMediaRepository::getBySearcherMediaId($media->id);
        $newImage = !empty($images) ? reset($images) : [];

        return response()->json($newImage ?? [], Response::HTTP_OK);
    }

    public function delete(SearcherMedia $media, SearcherMediaService $mediaService): JsonResponse
    {
        $mediaService->delete($media);
        return response()->json(['success' => true], Response::HTTP_OK);
    }

    public function uploadAvatar(SearcherProfile $profile, SearcherMediaService $mediaService): JsonResponse
    {
        $imagePath = $mediaService->uploadAvatar($profile);

        return response()->json('/' . $imagePath, Response::HTTP_OK);
    }

    public function uploadVideo(SearcherProfile $profile, SearcherMediaService $mediaService): JsonResponse
    {
        $media = $mediaService->uploadVideo($profile);

        $videoUrl = SearcherMediaFileRepository::getVideoFilePath($profile->id, $media->files()->first()->name);

        return response()->json([
            'id' => $media->id,
            'name' => $media->name,
            'url' => $videoUrl,
        ], Response::HTTP_OK);
    }
}
