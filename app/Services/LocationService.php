<?php

namespace App\Services;

use App\Models\SpCities;
use App\Models\SpCitySuburbs;
use App\Repositories\SearcherProfileRepository;
use Illuminate\Support\Facades\DB;

class LocationService extends Service
{
    public function updateSearcherProfileLocations(int $profileId, array $locations)
    {
        DB::beginTransaction();

        try {
            SearcherProfileRepository::deleteAllLocations($profileId);

            foreach ($locations as $location) {
                $spCity = new SpCities();
                $spCity->searcher_profile_id = $profileId;
                $spCity->city_id = $location['id'];
                $spCity->save();

                foreach ($location['parts'] as $part) {
                    $spCitySuburb = new SpCitySuburbs();
                    $spCitySuburb->sp_city_id = $spCity->id;
                    $spCitySuburb->suburb_id = $part['id'];
                    $spCitySuburb->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
