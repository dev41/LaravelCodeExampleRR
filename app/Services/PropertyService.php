<?php

namespace App\Services;

use App\Formatters\PropertyFormatter;
use App\Formatters\PropertyListFormatter;
use App\Models\Property;
use App\Models\PropertyParam;
use App\Models\PropertyRight;
use App\Models\Room;
use App\Repositories\PropertyRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class PropertyService
 * @package App\Services
 */
class PropertyService extends Service
{
    public function getAll()
    {
        $propertiesData = PropertyRepository::getAll(Auth::user()->getAuthIdentifier());

        return PropertyListFormatter::responseObject($propertiesData);
    }

    public function getById(Property $property)
    {
        /** @var RoomService $roomService */
        $roomService = resolve(RoomService::class);

        $propertyData = PropertyRepository::getById($property->id);
        $propertyFormatted = PropertyFormatter::responseObject($propertyData);

        $rooms = $property->rooms()->where(['rooms.status' => Room::STATUS_ACTIVE])->get();
        $formattedRooms = [];

        /** @var Room $room */
        foreach ($rooms as $room) {
            $formattedRooms[] = $roomService->getById($room->id, $propertyData)['room'];
        }

        $propertyFormatted['roomList'] = $formattedRooms;

        return $propertyFormatted;
    }

    public function createTmp(): Property
    {
        $this->deleteOldTmpProperties();

        try {
            DB::beginTransaction();

            $property = new Property();
            $property->creator_id = Auth::user()->getAuthIdentifier();
            $property->title = 'temporary';
            $property->lat = 0;
            $property->lon = 0;
            $property->type = Property::TYPE_FLAT;
            $property->parking = Property::PARKING_NO;
            $property->radius = 0;
            $property->address = '';
            $property->quite_time = '';

            $property->save();

            $propertyRight = new PropertyRight([
                'property_id' => $property->id,
                'user_id' => Auth::user()->getAuthIdentifier(),
                'role_id' => PropertyRight::PROPERTY_OWNER,
            ]);

            $propertyRight->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $property;
    }

    public function delete(Property $property)
    {
        /** @var RoomService $roomService */
        $roomService = resolve(RoomService::class);
        $roomService->deleteByProperty($property);

        $property->delete();
    }

    public function deleteOldTmpProperties()
    {
        Property::where('status', '=', Property::STATUS_TMP)
            ->where('created_at', '<', Carbon::now()->subDay())
            ->delete();
    }

    /**
     * @param Property $property
     * @param array $data
     * @return Property
     * @throws \Exception
     */
    public function update(Property $property, array $data)
    {
        try {
            DB::beginTransaction();

            $property->fill($data);
            $property->status = Property::STATUS_ACTIVE;
            $property->save();

            foreach (PropertyParam::FIELDS as $paramId => $paramName) {
                if (!isset($data[$paramName]) || !is_array($data[$paramName])) {
                    continue;
                }

                if (Property::PARAMS[$paramName]['type'] === 'single') {
                    $this->updateSingle($property, $data[$paramName], $paramId, $paramName);
                }

                if (Property::PARAMS[$paramName]['type'] === 'multiple') {
                    $this->updateMultiple($property, $data[$paramName], $paramId, $paramName);
                }
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $property;
    }

    /**
     * @param $paramName
     * @param $valueName
     * @return bool|int|string
     */
    private function getValueId($paramName, $valueName)
    {
        foreach (Property::PARAMS[$paramName]['values'] as $key => $value) {
            if($value['name'] === $valueName) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param $property
     * @param $data
     * @param $paramId
     * @param $paramName
     */
    private function updateSingle($property, $data, $paramId, $paramName)
    {
        if(empty($data)) {
            PropertyParam::where([
                'property_id' => $property->id,
                'param_id' => $paramId,
            ])->delete();
        } else {
            $propertyParam = PropertyParam::where([
                'property_id' => $property->id,
                'param_id' => $paramId,
                'param_value' => $this->getValueId($paramName, $data['name'])
            ])->first();

            if(empty($propertyParam)) {
                $propertyParam = new PropertyParam([
                    'property_id' => $property->id,
                    'param_id' => $paramId,
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
                $propertyParam->save();
            } else {
                PropertyParam::where([
                    'property_id' => $property->id,
                    'param_id' => $paramId,
                ])->update([
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
            }
        }
    }

    private function updateMultiple(Property $property, array $data, $paramId, $paramName)
    {
        $receivedParamValues = [];
        foreach ($data as &$item) {
            if(empty($item)) {
                continue;
            }

            $n = $this->getValueId($paramName, $item['name']);
            $item['paramId'] = $n;
            $receivedParamValues[$n] = $item['value'];
        }

        $savedParamValues = PropertyParam::where([
            'property_id' => $property->id,
            'param_id' => $paramId,
        ])->get()->pluck('param_value')->all();

        $deletedParamValues = array_diff($savedParamValues, array_keys($receivedParamValues));
        $addedParamValues = array_diff(array_keys($receivedParamValues), $savedParamValues);
        $updatedParamValues = array_diff(array_diff($savedParamValues, $addedParamValues), $deletedParamValues);

        foreach ($updatedParamValues as $value) {
            PropertyParam::where([
                'property_id' => $property->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->update(['element_value' => $receivedParamValues[$value]]);
        }

        foreach ($addedParamValues as $value) {
            $propertyParam = new PropertyParam([
                'property_id' => $property->id,
                'param_id' => $paramId,
                'param_value' => $value,
                'element_value' => $receivedParamValues[$value],
            ]);
            $propertyParam->save();
        }

        foreach ($deletedParamValues as $value) {
            PropertyParam::where([
                'property_id' => $property->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->delete();
        }
    }
}
