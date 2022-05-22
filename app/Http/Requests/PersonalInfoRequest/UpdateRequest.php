<?php

namespace App\Http\Requests\PersonalInfoRequest;

use App\Http\Requests\Request;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\PersonalInfoRequest
 *
 * @property string address
 * @property string birthday
 * @property int country
 * @property string email
 * @property string first_name
 * @property string id_number
 * @property int id_type
 * @property string last_name
 * @property string middle_name
 * @property string phone
 */
class UpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'chat_id' => $jsonArray['chatId'] ?? 0,
            'address' => $jsonArray['address'] ?? '',
            'birthday' => $jsonArray['birthday'] ? date('Y-m-d H:i:s', strtotime($jsonArray['birthday'])) : '',
            'country' => $jsonArray['country'] ?? 0,
            'email' => $jsonArray['email'] ?? '',
            'first_name' => $jsonArray['firstName'] ?? '',
            'id_number' => $jsonArray['idNumber'] ?? '',
            'id_type' => $jsonArray['idType'] ?? 0,
            'last_name' => $jsonArray['lastName'] ?? '',
            'middle_name' => $jsonArray['middleName'] ?? '',
            'phone' => $jsonArray['phone'] ?? '',
            'emergency_name' => $jsonArray['emergency']['name'] ?? '',
            'emergency_phone' => $jsonArray['emergency']['phone'] ?? '',
        ];

        $this->replace($parameters);
    }

    public function rules()
    {
        return [
            'chat_id' => 'required|integer',
            'address' => 'required|string',
            'birthday' => 'required|string',
            'country' => 'required|integer',
            'email' => 'required|string',
            'first_name' => 'required|string',
            'id_number' => 'required|string',
            'id_type' => 'required|integer',
            'last_name' => 'required|string',
            'middle_name' => 'string',
            'phone' => 'required|string',
            'emergency_name' => 'required|string',
            'emergency_phone' => 'required|string',
        ];
    }
}
