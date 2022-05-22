<?php

namespace App\Http\Requests\PaymentRequest;

use App\Http\Requests\Request;
use App\Models\PaymentRequest;

/**
 * @property array $toUserIds
 * @property int $rent
 * @property int $bond
 * @property int $deposit
 * @property int $purpose
 * @property string $expired_at
 * @property string $move_in_date
 *
 */
class SUUpdateRequest extends Request
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
            // related attributes
            'toUserIds' => $jsonArray['userId'] ? [$jsonArray['userId']] : [],

            'rent' => $jsonArray['rent'] ?? 0,
            'bond' => $jsonArray['bond'] ?? 0,
            'deposit' => $jsonArray['deposit'] ?? 0,

            // payment request data
            'purpose' => $jsonArray['purpose'] ?? 0,
            'expired_at' => $jsonArray['expirationDate'] ? date('Y-m-d H:i:s', strtotime($jsonArray['expirationDate'])) : '',
            'move_in_date' => $jsonArray['moveDate'] ? date('Y-m-d H:i:s', strtotime($jsonArray['moveDate'])) : '',
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'toUserIds' => 'required|array',

            'rent' => 'integer',
            'bond' => 'integer',
            'deposit' => 'integer',

            'purpose' => 'required|integer|in:' . implode(',', array_keys(PaymentRequest::TYPES)),
            'expired_at' => 'required|date_format:Y-m-d H:i:s',
            'move_in_date' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
