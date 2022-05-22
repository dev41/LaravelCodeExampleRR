<?php

namespace App\Http\Requests\OnboardingPaymentRequest;

use App\Http\Requests\Request;

/**
 * @property int $chat_id
 * @property string $expire_in
 * @property string $move_date
 * @property int $rent
 * @property int $bond
 * @property int $purpose
 */
class UpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'chat_id' => $jsonArray['chatId'] ?? 0,
            'expire_in' => !empty($jsonArray['expirationDate']) ? date('Y-m-d H:i:s', strtotime($jsonArray['expirationDate'])) : null,
            'move_date' => $jsonArray['moveDate'] ? date('Y-m-d H:i:s', strtotime($jsonArray['moveDate'])) : null,
            'rent' => $jsonArray['rent'] ?? 0,
            'bond' => $jsonArray['bond'] ?? 0,
            'purpose' => $jsonArray['purpose'] ?? 0,
        ];

        $this->replace($parameters);
    }

    public function rules()
    {
        return [
            'chat_id' => 'required|integer',
            'purpose' => 'required|integer',
            'rent' => 'integer',
            'bond' => 'integer',
            'move_date' => 'required|string',
            'expire_in' => 'string|nullable',
        ];
    }
}
