<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\CreateMediaRequest;
use App\Http\Requests\Room\UpdateMediaRequest;
use App\Models\Room;
use App\Models\RoomMedia;
use App\Repositories\RoomMediaFileRepository;
use App\Repositories\RoomMediaRepository;
use App\Services\RoomMediaService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoomMediaController extends Controller
{
    const CACHE_KEY = 'api.room.media';

    public function create(Room $room, CreateMediaRequest $request, RoomMediaService $roomMediaService): JsonResponse
    {
        $roomMedia = $roomMediaService->create($room, $request->type);

        $images = RoomMediaRepository::getByRoomMediaId($roomMedia->id);
        $newImage = !empty($images) ? reset($images) : [];

        return response()->json($newImage ?? [], Response::HTTP_OK);
    }

    public function delete(RoomMedia $roomMedia, RoomMediaService $roomMediaService): JsonResponse
    {
        $roomMediaService->delete($roomMedia);
        return response()->json(['success' => true], Response::HTTP_OK);
    }

    public function uploadVideo(Room $room, RoomMediaService $roomMediaService): JsonResponse
    {
        $roomMedia = $roomMediaService->uploadVideo($room);

        $videoUrl = RoomMediaFileRepository::getVideoFilePath($room->id, $roomMedia->files()->first()->name);

        return response()->json([
            'id' => $roomMedia->id,
            'name' => $roomMedia->name,
            'url' => $videoUrl,
        ], Response::HTTP_OK);
    }
}
