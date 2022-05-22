<?php

namespace App\Http\Requests\OnboardingAgreements;

use App\Http\Requests\Request;

/**
 * Class CreateTmpRequest
 * @package App\Http\Requests\OnboardingAgreements
 *
 * @property string emergency_repair_name
 * @property string emergency_repair_phone
 * @property string full_name
 * @property int rent
 * @property int bond
 * @property int bills_amount
 * @property int bills_included
 * @property string move_date
 * @property string expire_in
 * @property int lease
 *
 */
class UpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'emergency_repair_name' => $jsonArray['emergencyRepair']['name'] ?? '',
            'emergency_repair_phone' => $jsonArray['emergencyRepair']['phone'] ?? '',
            'full_name' => $jsonArray['offer']['fullName'] ?? '',
            'rent' => $jsonArray['offer']['rent'] ?? 0,
            'bond' => $jsonArray['offer']['bond'] ?? 0,
            'bills_amount' => $jsonArray['offer']['billsAmount'] ?? 0,
            'bills_included' => (int) (($jsonArray['offer']['billsIncluded'] ?? null) === 'true'),
            'move_date' => $jsonArray['offer']['moveIn'] ? date('Y-m-d H:i:s', strtotime($jsonArray['offer']['moveIn'])) : '',
            'expire_in' => $jsonArray['offer']['agreementEndDate'] ? date('Y-m-d H:i:s', strtotime($jsonArray['offer']['agreementEndDate'])) : '',
            'lease' => $jsonArray['offer']['lease'] ?? 0,
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'emergency_repair_name' => 'string',
            'emergency_repair_phone' => 'string',
            'full_name' => 'string',
            'rent' => 'integer',
            'bond' => 'integer',
            'bills_amount' => 'integer',
            'bills_included' => 'integer',
            'move_date' => 'string',
            'expire_in' => 'string',
            'lease' => 'integer',
        ];
    }
}
