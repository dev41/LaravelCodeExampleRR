<?php

namespace App\Http\Requests\SuperUserProfile;

use App\Http\Requests\Request;

/**
 * Class UpdateRequest
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $company_name
 * @property string $company_address
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
            'first_name' => $jsonArray['firstName'] ?? '',
            'last_name' => $jsonArray['lastName'] ?? '',
            'phone' => $jsonArray['phone'] ?? '',
            'company_name' => $jsonArray['companyName'] ?? '',
            'company_address' => $jsonArray['companyAddress'] ?? '',
        ];

        if (!empty($jsonArray['social'])) {
            $socials = array_combine(array_column($jsonArray['social'], 'name'), array_column($jsonArray['social'], 'link'));

            $parameters['twitter'] = $socials['twitter'] ?? '';
            $parameters['facebook'] = $socials['facebook'] ?? '';
            $parameters['instagram'] = $socials['instagram'] ?? '';
            $parameters['youtube'] = $socials['youtube'] ?? '';
        }

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'string',
            'company_name' => 'string',
            'company_address' => 'string',
        ];
    }
}
