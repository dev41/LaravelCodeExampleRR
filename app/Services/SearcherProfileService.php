<?php

namespace App\Services;

use App\Formatters\SearcherProfileFormatter;
use App\Models\SearcherProfile;
use App\Models\SearcherProfileParam;
use App\Models\User;
use App\Repositories\SearcherMediaRepository;
use App\Repositories\SearcherProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class ProfileService
 * @package App\Services
 */
class SearcherProfileService extends Service
{
    public function getById($userId)
    {
        $user = UserRepository::getById($userId);
        $profile = SearcherProfileRepository::getByIdWithParams($userId);
        $medias = SearcherMediaRepository::getByProfileId($userId);

        return SearcherProfileFormatter::responseObject($user, $profile, $medias);
    }

    public function createEmpty(int $userId)
    {
        return SearcherProfileRepository::createEmpty($userId);
    }

    /**
     * @param SearcherProfile $profile
     * @param array $data
     * @return SearcherProfile
     * @throws \Exception
     */
    public function update(SearcherProfile $profile, array $data)
    {
        try {
            DB::beginTransaction();

            $profile->fill($data);
            $profile->status = SearcherProfile::STATUS_FILLED;
            $profile->save();

            /** @var User $user */
            $user = $profile->user()->first();
            $user->fill($data);
            $user->save();

            /** @var LocationService $locationService */
            $locationService = resolve(LocationService::class);
            $locationService->updateSearcherProfileLocations($profile->id, $data['locations'] ?? []);

            SearcherMediaRepository::updateImagePositions(array_column($data['images'], 'id'));

            foreach (SearcherProfileParam::FIELDS as $paramId => $paramName) {
                if (!isset($data[$paramName]) || !is_array($data[$paramName])) {
                    continue;
                }

                if (SearcherProfile::PARAMS[$paramName]['type'] === 'single') {
                    $this->updateSingle($profile, $data[$paramName], $paramId, $paramName);
                } elseif (SearcherProfile::PARAMS[$paramName]['type'] === 'multiple') {
                    $this->updateMultiple($profile, $data[$paramName], $paramId, $paramName);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $profile;
    }

    /**
     * @param $profile
     * @param $data
     * @param $paramId
     * @param $paramName
     */
    private function updateSingle($profile, $data, $paramId, $paramName)
    {
        if (empty($data)) {
            SearcherProfileParam::where([
                'profile_id' => $profile->id,
                'param_id' => $paramId,
            ])->delete();
        } else {
            $profileParam = SearcherProfileParam::where([
                'profile_id' => $profile->id,
                'param_id' => $paramId,
            ])->first();

            if (empty($profileParam)) {
                $profileParam = new SearcherProfileParam([
                    'profile_id' => $profile->id,
                    'param_id' => $paramId,
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
                $profileParam->save();
            } else {
                SearcherProfileParam::where([
                    'profile_id' => $profile->id,
                    'param_id' => $paramId,
                ])->update([
                    'param_value' => $this->getValueId($paramName, $data['name']),
                    'element_value' => $data['value'] ?? null,
                ]);
            }
        }
    }

    /**
     * @param $profile
     * @param $data
     * @param $paramId
     * @param $paramName
     */
    private function updateMultiple($profile, $data, $paramId, $paramName)
    {
        $receivedParamValues = [];
        foreach ($data as &$item) {
            if (empty($item)) {
                continue;
            }

            $n = $this->getValueId($paramName, $item['name']);
            $item['paramId'] = $n;
            $receivedParamValues[$n] = $item['value'] ?? $item['tooltip'] ?? '';
        }

        $savedParamValues = SearcherProfileParam::where([
            'profile_id' => $profile->id,
            'param_id' => $paramId,
        ])->get()->pluck('param_value')->all();

        $deletedParamValues = array_diff($savedParamValues, array_keys($receivedParamValues));
        $addedParamValues = array_diff(array_keys($receivedParamValues), $savedParamValues);
        $updatedParamValues = array_diff(array_diff($savedParamValues, $addedParamValues), $deletedParamValues);

        foreach ($updatedParamValues as $value) {
            SearcherProfileParam::where([
                'profile_id' => $profile->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->update(['element_value' => $receivedParamValues[$value]]);
        }

        foreach ($addedParamValues as $value) {
            $propertyParam = new SearcherProfileParam([
                'profile_id' => $profile->id,
                'param_id' => $paramId,
                'param_value' => $value,
                'element_value' => $receivedParamValues[$value],
            ]);
            $propertyParam->save();
        }

        foreach ($deletedParamValues as $value) {
            SearcherProfileParam::where([
                'profile_id' => $profile->id,
                'param_id' => $paramId,
                'param_value' => $value,
            ])->delete();
        }
    }

    /**
     * @param $paramName
     * @param $valueName
     * @return bool|int|string
     */
    private function getValueId($paramName, $valueName)
    {
        foreach (SearcherProfile::PARAMS[$paramName]['values'] as $key => $value) {
            if ($value['name'] === $valueName) {
                return $key;
            }
        }

        return false;
    }

}
