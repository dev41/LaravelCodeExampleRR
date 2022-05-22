<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\UpdateRequest;
use App\Libraries\Cache;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use App\Services\AuthService;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    const CACHE_KEY = 'api.room';

    public function getById(int $roomId, RoomService $roomService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $roomId;
        $room = Cache::getSet($key, function() use ($roomService, $roomId) {
            return $roomService->getById($roomId);
        });

        return response()->json($room, Response::HTTP_OK);
    }

    public function createTemporary(Property $property, RoomService $roomService): JsonResponse
    {
        $room = $roomService->createTmp($property);

        return response()->json([
            'id' => $room->id,
            'letter' => $room->letter,
        ], Response::HTTP_CREATED);
    }

    public function update(Room $room, UpdateRequest $request, RoomService $roomService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $room->id;
        $res = $roomService->update($room, $request->toArray());
        Cache::delete($key);

        return response()->json($res, Response::HTTP_OK);
    }

    public function delete(Room $room, RoomService $roomService): JsonResponse
    {
        $roomService->delete($room);
        return response()->json(['success' => true], Response::HTTP_OK);
    }

    public function chat(int $userId, AuthService $authService)
    {
        $user = User::find($userId);

        return view('home', [
            'user' => $user,
        ]);
    }

}
