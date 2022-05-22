<?php

namespace App\Http\Requests\PaymentRequest;

use App\Http\Requests\Request;

/**
 * Class CreateTmpRequest
 * @package App\Http\Requests\PaymentRequest
 *
 * @property int $roomId
 * @property int $userId
 *
 */
class CreateTmpRequest extends Request
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
            'roomId' => 'required|integer',
            'userId' => 'integer',
        ];
    }
}
