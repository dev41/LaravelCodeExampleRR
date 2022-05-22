<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\Request;
use App\Models\Room;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Room
 */
class UpdateRequest extends Request
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $jsonArray = $this->post();

        array_walk_recursive($jsonArray, function(&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        $parameters = [
            // room table attributes
            'title' => $jsonArray['general']['title'] ?? '',
            'videoYoutube' => $jsonArray['videoYoutube'] ?? '',
            'our_story' => $jsonArray['story'] ?? '',
            'size' => $jsonArray['features']['specification']['roomSize']['value'] ?? '',
            'date_available' => $jsonArray['features']['specification']['dateAvailable']['date'] ? date('Y-m-d H:i:s', strtotime($jsonArray['features']['specification']['dateAvailable']['date'])) : '',
            'date_available_text' => $jsonArray['features']['specification']['dateAvailable']['value'] ?? '',
            'rent_amount' => $jsonArray['general']['rentAmount'] ?? '',
            'consumablesIncluded' => (int) (($jsonArray['general']['consumablesIncluded'] ?? null) === 'true'),
            'consumablesValue' => $jsonArray['general']['consumablesValue'] ?? '',
            'internet_speed' => $jsonArray['features']['internet']['settings']['speed'] ?? '',
            'internet_unlimited' => (int) (($jsonArray['features']['internet']['settings']['unlimited'] ?? null) === 'true'),

            'water' => (int) (($jsonArray['consumables']['main']['water'] ?? null) === 'true'),
            'electricity' => (int) (($jsonArray['consumables']['main']['electricity'] ?? null) === 'true'),
            'gas' => (int) (($jsonArray['consumables']['main']['gas'] ?? null) === 'true'),

            // parameters
            'rent' => ['name' => $jsonArray['general']['rent'] ?? ''],

            'consumables_included' => $jsonArray['consumables']['extras'] ?? [],

            'is_services_included' => (int) (($jsonArray['consumables']['servicesIncluded'] ?? null) === 'true'),
            'services_included' => $jsonArray['consumables']['services'] ?? [],

            // parameters:features:specification

            'type' => ['name' => $jsonArray['features']['specification']['roomType']['value'] ?? ''],
            'bond' => ['name' => $jsonArray['features']['specification']['bond']['value'] ?? ''],
            'rental_period' => ['name' => $jsonArray['features']['specification']['minRentalPeriod']['value'] ?? ''],

            // parameters:features:internet

            'internet_connection_type' => ['name' => $jsonArray['features']['internet']['settings']['connectionType'] ?? ''],
            'internet' => ['name' => $jsonArray['features']['internet']['settings']['value'] ?? ''],

            // parameters:features:comfort

            'furnishing' => ['name' => $jsonArray['features']['comfort']['furnishing']['value'] ?? ''],
            'bed_size' => ['name' => $jsonArray['features']['comfort']['bed']['value'] ?? ''],
            'bathroom' => [
                'name' => $jsonArray['features']['comfort']['bathroom']['value'] ?? '',
                'value' => $jsonArray['features']['comfort']['bathroom']['tooltip'] ?? '',
            ],
            'kitchenette' => [
                'name' => $jsonArray['features']['comfort']['kitchen']['value'] ?? '',
                'value' => $jsonArray['features']['comfort']['bathroom']['tooltip'] ?? '',
            ],

            'features' => $jsonArray['features']['roomFeatures'] ?? [],
            'accepting' => $jsonArray['features']['preferences'] ?? [],

            'images' => $jsonArray['images'] ?? [],
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'videoYoutube' => 'string',
            'our_story' => 'string',
            'size' => 'string',
            'rent_amount' => 'required|integer',
            'consumablesIncluded' => 'integer',
            'consumablesValue' => 'string',
            'water' => 'integer',
            'electricity' => 'integer',
            'gas' => 'integer',
            'date_available' => 'required|date_format:Y-m-d H:i:s',
            'date_available_text' => 'required|string',
            'internet_speed' => 'string',
            'internet_unlimited' => 'integer',

            // medias

            'images' => 'required|array',

            // parameters

            'rent' => 'required|array',
            'rent.name' => 'required|string|in:' . $this->getParamNames(Room::RENT),
            'rent.value' => 'string|nullable',

            'consumables_included' => 'sometimes|array',
            'consumables_included.*' => 'array',
            'consumables_included.*.name' => 'required_with:consumablesIncluded|string|in:' . $this->getParamNames(Room::CONSUMABLES_INCLUDED),
            'consumables_included.*.value' => 'string|nullable',

            'is_services_included' => 'integer',

            'services_included' => 'sometimes|array',
            'services_included.*' => 'array',
            'services_included.*.name' => 'required_with:is_services_included|string|in:' . $this->getParamNames(Room::SERVICES_INCLUDED),
            'services_included.*.value' => 'string|nullable',

            // parameters:features:specification

            'type' => 'required|array',
            'type.name' => 'required|string|in:' . $this->getParamNames(Room::TYPE),
            'type.value' => 'string|nullable',

            'bond' => 'required|array',
            'bond.name' => 'required|string|in:' . $this->getParamNames(Room::BOND),
            'bond.value' => 'string|nullable',

            'rental_period' => 'required|array',
            'rental_period.name' => 'required|string|in:' . $this->getParamNames(Room::RENTAL_PERIOD),
            'rental_period.value' => 'string|nullable',

            // parameters:features:internet

            'internet_connection_type' => 'array',
            'internet_connection_type.name' => 'nullable|string|in:' . $this->getParamNames(Room::INTERNET_TYPE),
            'internet_connection_type.value' => 'string|nullable',

            'internet' => 'array',
            'internet.name' => 'string|in:' . $this->getParamNames(Room::INTERNET),
            'internet.value' => 'string|nullable',

            // parameters:features:comfort

            'furnishing' => 'array',
            'furnishing.name' => 'string|in:' . $this->getParamNames(Room::FURNISHING),
            'furnishing.value' => 'string|nullable',

            'bed_size' => 'array',
            'bed_size.name' => 'string|in:' . $this->getParamNames(Room::BED_SIZE),
            'bed_size.value' => 'string|nullable',

            'bathroom' => 'array',
            'bathroom.name' => 'string|in:' . $this->getParamNames(Room::BATHROOM),
            'bathroom.value' => 'string|nullable',

            'kitchenette' => 'array',
            'kitchenette.name' => 'string|in:' . $this->getParamNames(Room::KITCHENETTE),
            'kitchenette.value' => 'string|nullable',


            'features' => 'required|array',
            'features.*' => 'array',
            'features.*.name' => 'required|string|in:' . $this->getParamNames(Room::FEATURES),
            'features.*.value' => 'string|nullable',

            'accepting' => 'array',
            'accepting.*' => 'array',
            'accepting.*.name' => 'required|string|in:' . $this->getParamNames(Room::ACCEPTING),
            'accepting.*.value' => 'string|nullable',
        ];
    }
}
