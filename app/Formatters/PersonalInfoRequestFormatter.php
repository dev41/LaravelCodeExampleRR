<?php

namespace App\Formatters;

class PersonalInfoRequestFormatter extends Formatter
{
    public static function responseObject($data): array
    {
        return [
            'id' => $data->id,
            'address' => $data->address,
            'birthday' => $data->birthday,
            'country' => $data->country,
            'email' => $data->email,
            'firstName' => $data->first_name,
            'idNumber' => $data->id_number,
            'idType' => $data->id_type,
            'lastName' => $data->last_name,
            'middleName' => $data->middle_name,
            'phone' => $data->phone,
        ];
    }
}
