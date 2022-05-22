<?php

namespace App\Http\Requests\Offer;

use App\Http\Requests\Request;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Offer
 *
 * @property integer $rent_amount
 * @property integer $bond
 * @property integer $bills_amount
 * @property integer $bills_included
 * @property integer $expires_in
 * @property integer $move_in_date
 * @property integer $length_of_lease
 * @property integer $searcher_id
 * @property integer $chat_id
 *
 */
class UpdateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'rent_amount' => $jsonArray['rent'] ?? 0,
            'bond' => $jsonArray['bond'] ?? 0,
            'bills_amount' => $jsonArray['billsAmount'] ?? 0,
            'bills_included' => (int) (($jsonArray['billsIncluded'] ?? null) === 'true'),
            'expires_in' => $jsonArray['expirationDate'] ?? 0,
            'move_in_date' => $jsonArray['moveDate'] ? date('Y-m-d H:i:s', strtotime($jsonArray['moveDate'])) : '',
            'length_of_lease' => $jsonArray['leaseLength'] ?? 0,
            'searcher_id' => $jsonArray['searcherId'] ?? 0,
            'chat_id' => $jsonArray['chatId'] ?? 0,
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'rent_amount' => 'required|integer',
            'bond' => 'required|integer',
            'bills_amount' => 'required|integer',
            'bills_included' => 'required|integer',
            'expires_in' => 'required|integer',
            'move_in_date' => 'required|date_format:Y-m-d H:i:s',
            'length_of_lease' => 'required|integer',
            'searcher_id' => 'required|integer',
            'chat_id' => 'required|integer',
        ];
    }
}
