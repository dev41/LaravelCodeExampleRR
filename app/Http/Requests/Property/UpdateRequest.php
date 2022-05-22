<?php

namespace App\Http\Requests\Property;

use App\Http\Requests\Request;

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
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            // property attributes
            'title' => $jsonArray['title'] ?? '',
            'address' => $jsonArray['map']['address'] ?? '',
            'lat' => $jsonArray['map']['location'][0] ?? 0,
            'lon' => $jsonArray['map']['location'][1] ?? 0,
            'radius' => $jsonArray['map']['radius'] ?? '',
            'transport' => $jsonArray['options']['transport']['value'] ?? '',
            'quite_time' => $jsonArray['features']['quiteTime']['value'] ?? '',

            // single parameters
            'type' => ['name' => $jsonArray['type']['propertyType']['value'] ?? ''],
            'parking' => ['name' => $jsonArray['options']['parking']['value'] ?? ''],
            'stay_house' => ['name' => $jsonArray['features']['stayHouse']['name'] ?? ''],
            'heating' => ['name' => $jsonArray['features']['heating']['value'] ?? ''],
            'cooling' => ['name' => $jsonArray['features']['cooling']['value'] ?? ''],

            // multiple parameters
            'features' => $jsonArray['features']['propertyFeatures'] ?? [],
            'rules' => $jsonArray['features']['basicRules'] ?? [],
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
