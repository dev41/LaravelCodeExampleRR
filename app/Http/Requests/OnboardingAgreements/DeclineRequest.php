<?php

namespace App\Http\Requests\OnboardingAgreements;

use App\Http\Requests\Request;

/**
 * Class CreateTmpRequest
 * @package App\Http\Requests\OnboardingAgreements
 *
 * @property integer $messageId
 *
 */
class DeclineRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'messageId' => 'integer|required',
        ];
    }
}
