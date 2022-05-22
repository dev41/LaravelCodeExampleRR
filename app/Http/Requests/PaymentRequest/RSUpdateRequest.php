<?php

namespace App\Http\Requests\PaymentRequest;

use App\Http\Requests\Request;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $birthday
 * @property string $id_number
 * @property string $stripe_card_token
 * @property int $messageId
 */
class RSUpdateRequest extends Request
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
            'middle_name' => $jsonArray['middleName'] ?? '',
            'birthday' => $jsonArray['birthday'] ? date('Y-m-d H:i:s', strtotime($jsonArray['birthday'])) : '',
            'id_number' => $jsonArray['idNumber'] ?? '',
            'stripe_card_token' => $jsonArray['cardToken'] ?? '',
            'messageId' => $jsonArray['messageId'] ?? '',
        ];

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
            'middle_name' => 'string',
            'birthday' => 'required|date_format:Y-m-d H:i:s',
            'id_number' => 'required|string',
            'stripe_card_token' => 'required|string',
            'messageId' => 'integer',
        ];
    }
}
