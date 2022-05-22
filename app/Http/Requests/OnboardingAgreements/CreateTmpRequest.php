<?php

namespace App\Http\Requests\OnboardingAgreements;

use App\Http\Requests\Request;

/**
 * Class CreateTmpRequest
 * @package App\Http\Requests\OnboardingAgreements
 *
 * @property int $chat_id
 * @property int $searcher_id
 *
 */
class CreateTmpRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'chat_id' => $jsonArray['chatId'] ?? null,
            'searcher_id' => $jsonArray['searcherId'] ?? null,
        ];

        $this->replace($parameters);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'chat_id' => 'required|integer',
            'searcher_id' => 'required|integer',
        ];
    }
}
