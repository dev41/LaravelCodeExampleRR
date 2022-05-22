<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\Request;
use App\Models\Room;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Room
 */
class CreateRequest extends Request
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'rent' => 'required|array',
            'rent.name' => 'required|string|in:' . $this->getParamNames(Room::RENT),
            'rent.value' => 'string|nullable',

            'rent_amount' => 'required|integer',

            'bond' => 'required|array',
            'bond.name' => 'required|string|in:' . $this->getParamNames(Room::BOND),
            'bond.value' => 'string|nullable',

            'date_available' => 'required|string',

            'rental_period' => 'required|array',
            'rental_period.name' => 'required|string|in:' . $this->getParamNames(Room::RENTAL_PERIOD),
            'rental_period.value' => 'string|nullable',

            'size' => 'string',

            'room_size_unit' => 'sometimes|array',
            'room_size_unit.name' => 'required_with:room_size_unit|string|in:' . $this->getParamNames(Room::ROOM_SIZE_UNIT),
            'room_size_unit.value' => 'string|nullable',

            'type' => 'required|array',
            'type.name' => 'required|string|in:' . $this->getParamNames(Room::TYPE),
            'type.value' => 'string|nullable',

            'title' => 'required|string',

            'our_story' => 'string',

            'furnishing' => 'sometimes|array',
            'furnishing.name' => 'required_with:furnishing|string|in:' . $this->getParamNames(Room::FURNISHING),
            'furnishing.value' => 'string|nullable',

            'features' => 'required|array',
            'features.*' => 'array',
            'features.*.name' => 'required|string|in:' . $this->getParamNames(Room::FEATURES),
            'features.*.value' => 'string|nullable',

            'internet' => 'sometimes|array',
            'internet.name' => 'required_with:internet|string|in:' . $this->getParamNames(Room::INTERNET),
            'internet.value' => 'string|nullable',

            'internet_connection_type' => 'array',
            'internet_connection_type.name' => 'required|string|in:' . $this->getParamNames(Room::INTERNET_TYPE),
            'internet_connection_type.value' => 'string|nullable',

            'internet_speed' => 'string',

            'accepting' => 'required|array',
            'accepting.*' => 'array',
            'accepting.*.name' => 'required|string|in:' . $this->getParamNames(Room::ACCEPTING),
            'accepting.*.value' => 'string|nullable',

            'bathroom' => 'sometimes|array',
            'bathroom.name' => 'required_with:bathroom|string|in:' . $this->getParamNames(Room::BATHROOM),
            'bathroom.value' => 'string|nullable',

            'kitchenette' => 'sometimes|array',
            'kitchenette.name' => 'required_with:kitchenette|string|in:' . $this->getParamNames(Room::KITCHENETTE),
            'kitchenette.value' => 'string|nullable',

            'consumables' => 'sometimes|array',
            'consumables.name' => 'required_with:consumables|string|in:' . $this->getParamNames(Room::CONSUMABLES),
            'consumables.value' => 'string|nullable',

            'consumables_included' => 'sometimes|array',
            'consumables_included.*' => 'array',
            'consumables_included.*.name' => 'required_with:consumables_included|string|in:' . $this->getParamNames(Room::CONSUMABLES_INCLUDED),
            'consumables_included.*.value' => 'string|nullable',

            'services' => 'sometimes|array',
            'services.name' => 'required_with:services|string|in:' . $this->getParamNames(Room::SERVICES),
            'services.value' => 'string|nullable',

            'services_included' => 'array',
            'services_included.*' => 'array',
            'services_included.*.name' => 'required|string|in:' . $this->getParamNames(Room::SERVICES_INCLUDED),
            'services_included.*.value' => 'string|nullable',

            'bed_size' => 'sometimes|array',
            'bed_size.name' => 'required_with:bed_size|string|in:' . $this->getParamNames(Room::BED_SIZE),
            'bed_size.value' => 'string|nullable',

            'heating' => 'sometimes|array',
            'heating.name' => 'required_with:heating|string|in:' . $this->getParamNames(Room::HEATING),
            'heating.value' => 'string|nullable',

            'cooling' => 'sometimes|array',
            'cooling.name' => 'required_with:cooling|string|in:' . $this->getParamNames(Room::COOLING),
            'cooling.value' => 'string|nullable',
        ];
    }
}
