<?php

namespace App\Http\Requests\SearcherProfile;

use App\Http\Requests\Request;
use App\Models\SearcherProfile;
use App\Models\User;

/**
 * Class UpdateRequest
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
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [

            // profile and user tables attributes
            'video_youtube' => $jsonArray['videoYoutube'] ?? '',
            'story' => $jsonArray['story'] ?? '',
            'move_date' => $jsonArray['features']['main']['typical']['moveDate']['date'] ? date('Y-m-d H:i:s', strtotime($jsonArray['features']['main']['typical']['moveDate']['date'])) : '',
            'move_date_text' => $jsonArray['features']['main']['typical']['moveDate']['value'] ?? '',
            'first_name' => $jsonArray['main']['firstName'] ?? '',
            'last_name' => $jsonArray['main']['lastName'] ?? '',
            'gender' => self::getParamValue($jsonArray['main']['gender']['value'] ?? '', User::GENDERS),
            'age' => $jsonArray['main']['age'] ?? '',
            'children' => !empty($jsonArray['placeFor']['children']) ? (int) $jsonArray['placeFor']['children'] : null,
            'rent' => self::getParamValue($jsonArray['features']['main']['rent'] ?? '', SearcherProfile::RENT),
            'rent_amount' => $jsonArray['features']['main']['rentAmount'] ?? 0,
            'internet' => self::getParamValue($jsonArray['features']['lifestyle']['internet']['required']['value'] ?? '', SearcherProfile::INTERNET),

            // single parameters
            'place_for' => ['name' => $jsonArray['placeFor']['about']['value'] ?? ''],
            'rental_period' => ['name' => $jsonArray['features']['main']['typical']['rentalPeriod']['value'] ?? ''],
            'occupancies' => ['name' => $jsonArray['features']['main']['typical']['occupancies']['value'] ?? ''],
            'furnishing' => ['name' => $jsonArray['features']['options']['furnishing']['value'] ?? ''],
            'parking' => ['name' => $jsonArray['features']['options']['parking']['value'] ?? ''],
            'bathroom' => ['name' => $jsonArray['features']['options']['bathroom']['value'] ?? ''],
            'kitchen' => ['name' => $jsonArray['features']['options']['kitchen']['value'] ?? ''],

            // multiple parameters
            'lifestyle' => $this->getMultipleParamsWithTooltips(
                $jsonArray['features']['lifestyle']['set'] ?? [],
                $jsonArray['features']['lifestyle']['tooltips'] ?? []
            ),
            'internet_option' => $this->getMultipleParamsWithTooltips(
                $jsonArray['features']['lifestyle']['internet']['options'] ?? [],
                $jsonArray['features']['lifestyle']['internet']['tooltips'] ?? []
            ),
            'employment_status' => $this->getMultipleParamsWithTooltips(
                $jsonArray['features']['main']['employmentStatus']['options'] ?? [],
                $jsonArray['features']['main']['employmentStatus']['tooltips'] ?? []
            ),
            'preferences' => $jsonArray['features']['preferences'] ?? [],

            'images' => $jsonArray['images'] ?? [],

            'locations' => $jsonArray['features']['main']['location']['options'] ?? [],
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
