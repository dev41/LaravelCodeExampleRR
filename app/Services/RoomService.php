<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Formatters\RoomFormatter;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomParam;
use App\Models\RoomRight;
use App\Repositories\PropertyRepository;
use App\Repositories\RoomMediaRepository;
use App\Repositories\RoomRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class RoomService
 * @package App\Services
 */
class RoomService extends Service
{

    public function getById(int $roomId, array $propertyData = null): array
    {
        $roomData = RoomRepository::getById($roomId);
        if (empty($roomData)) {
            throw new ModelNotFoundException();
        }

        $propertyData = $propertyData ?? PropertyRepository::getById($roomData['el']['property_id']);
        $medias = RoomMediaRepository::getByRoomId($roomId);

        return RoomFormatter::responseObject($roomData, $propertyData, $medias);
    }

    public function createTmp(Property $property): Room
    {
        $this->deleteOldTmpRooms($property);

        try {
            DB::beginTransaction();

            $room = new Room();
            $room->property_id = $property->id;
            $room->creator_id = Auth::user()->getAuthIdentifier();
            $room->letter = $this->getNextRoomLetterByProperty($property);
            $room->rent_amount = 0;
            $room->date_available = new \DateTime();
            $room->date_available_text = '';
            $room->title = 'temporary';

            $room->save();

            $roomRight = new RoomRight([
                'room_id' => $room->id,
                'user_id' => Auth::user()->getAuthIdentifier(),
                'role_id' => RoomRight::PROPERTY_OWNER
            ]);
            $roomRight->save();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }

        return $room;
    }

    public function delete(Room $room)
    {
        $this->removeRoomsWithResources([$room]);
    }

    public function deleteByProperty(Property $property)
    {
        $rooms = $property->rooms()->get()->all();

        $this->removeRoomsWithResources($rooms);
    }

    public function deleteOldTmpRooms(Property $property)
    {
        $tmpRooms = $property->rooms()
            ->where(['rooms.status' => Room::STATUS_TMP])
            ->where('rooms.created_at', '<', Carbon::now()->subDay())
            ->get()->all();

        $this->removeRoomsWithResources($tmpRooms);
    }

    public function removeRoomsWithResources($rooms)
    {
        if (empty($rooms)) {
            return;
        }

        /** @var Room $room */
        foreach ($rooms as $room) {
            RoomMediaRepository::removeFilesFromStorageByRooms($room->id);
            $room->delete();
        }
    }

    public function getNextRoomLetterByProperty(Property $property): string
    {
        $rooms = $property->rooms()->select('letter')->orderBy('letter', 'desc')->get()->all();
        return self::getNextRoomLetterByRooms($rooms);
    }

    private static function getNextRoomLetterByRooms($rooms, $prefix = null): string
    {
        if (empty($rooms)) {
            return 'A';
        }

        $existLetters = array_column($rooms, 'letter');
        $letterScope = range('A', 'Z');

        if ($prefix) {
            $letterScope = array_map(function($letter) use ($prefix) {
                return $prefix . $letter;
            }, $letterScope);
        }

        $availableLetters = array_diff($letterScope, $existLetters);

        if (empty($availableLetters)) {
            $prefix = $prefix ? ++$prefix : 'A';
            return self::getNextRoomLetterByRooms($rooms, $prefix);
        }

        return reset($availableLetters);
    }

    /**
     * @param Room $room
     * @param array $data
     * @return Room
     * @throws \Exception
     */
    public function update(Room $room, array $data)
    {
        try {
            DB::beginTransaction();

            $room->fill($data);
            $room->status = Room::STATUS_ACTIVE;
            $room->save();

            RoomMediaRepository::updateImagePositions(array_column($data['images'], 'id'));

            foreach (RoomParam::FIELDS as $paramId => $paramName) {
                if (!isset($data[$paramName]) || !is_array($data[$paramName])) {
                    continue;
                }

                if (Room::PARAMS[$paramName]['type'] === 'single') {
                    $this->updateSingle($room, $data[$paramName], $paramId, $paramName);
                }

                if (Room::PARAMS[$paramName]['type'] === 'multiple') {
                    $this->updateMultiple($room, $data[$paramName], $paramId, $paramName);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $room;
    }

    /**
     * @param $paramName
     * @param $valueName
     * @return bool|int|string
     */
    private function getValueId($paramName, $valueName)
    {
        foreach (Room::PARAMS[$paramName]['values'] as $key => $value) {
            if ($value['name'] === $valueName) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param $room
     * @param $data
     * @param $paramId
     * @param $paramName
     */
    private function updateSingle($room, $data, $paramId, $paramName)
    {
        if (empty($data)) {
            RoomParam::where([
                'room_id' => $room->id,
                'param_id' => $paramId,
            ])->delete();
        } else {
            $roomParam = RoomParam::where([
                'room_id' => $room->id,
                'param_id' => $paramId,
            ])->first();

            if (empty($roomParam)) {
                $roomParam = new RoomParam([
                    'room_id' => $room->id,
                    'param_id' => $paramId,
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
                $roomParam->save();
            } else {
                RoomParam::where([
                    'room_id' => $room->id,
                    'param_id' => $paramId,
                ])->update([
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
            }
        }
    }

    /**
     * @param $room
     * @param $data
     * @param $paramId
     * @param $paramName
     */
    private function updateMultiple($room, $data, $paramId, $paramName)
    {
        $receivedParamValues = [];
        foreach ($data as &$item) {
            if (empty($item)) {
                continue;
            }

            $n = $this->getValueId($paramName, $item['name']);
            $item['paramId'] = $n;
            $receivedParamValues[$n] = $item['value'];
        }

        $savedParamValues = RoomParam::where([
            'room_id' => $room->id,
            'param_id' => $paramId,
        ])->get()->pluck('param_value')->all();
        $deletedParamValues = array_diff($savedParamValues, array_keys($receivedParamValues));
        $addedParamValues = array_diff(array_keys($receivedParamValues), $savedParamValues);
        $updatedParamValues = array_diff(array_diff($savedParamValues, $addedParamValues), $deletedParamValues);

        foreach ($updatedParamValues as $value) {
            RoomParam::where([
                'room_id' => $room->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->update(['element_value' => $receivedParamValues[$value]]);
        }

        foreach ($addedParamValues as $value) {
            $roomParam = new RoomParam([
                'room_id' => $room->id,
                'param_id' => $paramId,
                'param_value' => $value,
                'element_value' => $receivedParamValues[$value],
            ]);
            $roomParam->save();
        }

        foreach ($deletedParamValues as $value) {
            RoomParam::where([
                'room_id' => $room->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->delete();
        }
    }
}
