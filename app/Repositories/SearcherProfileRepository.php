<?php
namespace App\Repositories;

use App\Models\SearcherProfile;
use App\Models\SearcherProfileParam;
use App\Models\SpCities;
use Illuminate\Support\Facades\DB;

class SearcherProfileRepository extends Repository
{
    public static function getById(int $profileId): SearcherProfile
    {
        $profile = SearcherProfile::find($profileId);

        if (!$profile) {
            self::createEmpty($profileId);
            $profile = self::getById($profileId);
        }

        return $profile;
    }

    public static function getByIdWithParams(int $profileId)
    {
        $profile = DB::table('searcher_profiles')->select([
            'id',
            'avatar',
            'story',
            'video_youtube',
            'rent_amount',
            'rent',
            'children',
            'move_date',
            'move_date_text',
            'internet',
        ])->where([
            'id' => $profileId,
        ])->first();

        $profileLocations = DB::table('sp_cities as spc')
            ->select([
                'c.id as city_id',
                'c.name as city_name',
                's.id as suburb_id',
                's.name as suburb_name',
            ])
            ->leftJoin('sp_city_suburbs as spcs', 'spcs.sp_city_id', '=', 'spc.id')
            ->leftJoin('city as c', 'spc.city_id', '=', 'c.id')
            ->leftJoin('suburb as s', 'spcs.suburb_id', '=', 's.id')
            ->where(['spc.searcher_profile_id' => $profileId])
            ->get()
        ;
        $profileLocations = self::selectDataToArray($profileLocations);

        $locationsRes = [];
        foreach ($profileLocations as $location) {
            $cityId = $location['city_id'];

            if (empty($locationsRes[$cityId])) {
                $locationsRes[$cityId] = [
                    'id' => $location['city_id'],
                    'name' => $location['city_name'],
                    'parts' => [],
                ];
            }

            $locationsRes[$cityId]['parts'][] = [
                'id' => $location['suburb_id'],
                'name' => $location['suburb_name'],
            ];
        }

        $params = DB::table('searcher_profile_params')->select([
            'param_id',
            'param_value',
            'element_value',
        ])->where([
            'profile_id' => $profileId,
        ])->get()->toArray();

        $result = self::combineEntityWithParams($profile, $params, SearcherProfileParam::FIELDS, SearcherProfile::PARAMS);

        if (!empty($result)) {
            $result['locations'] = $locationsRes ?? [];
        }

        return empty($result) ? [] : $result;
    }

    public static function createEmpty(int $userId): SearcherProfile
    {
        $searcherProfile = new SearcherProfile();

        $searcherProfile->id = $userId;
        $searcherProfile->rent_amount = 0;
        $searcherProfile->rent = 0;
        $searcherProfile->move_date = new \DateTime();
        $searcherProfile->move_date_text = '';
        $searcherProfile->internet = SearcherProfile::INTERNET_FLEXIBLE;

        $searcherProfile->save();

        return $searcherProfile;
    }

    public static function deleteAllLocations(int $userId)
    {
        SpCities::where([
            'searcher_profile_id' => $userId,
        ])->delete();
    }
}
