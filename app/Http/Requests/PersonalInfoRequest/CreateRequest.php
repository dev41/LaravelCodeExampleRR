<?php

namespace App\Http\Requests\PersonalInfoRequest;

use App\Http\Requests\Request;

/**
 * Class CreateRequest
 * @package App\Http\Requests\PersonalInfoRequest
 *
 * @property int $chat_id
 * @property int $searcher_id
 */
class CreateRequest extends Request
{
    protected function prepareForValidation()
    {
        $jsonArray = $this->getTrimmedPostData();

        $parameters = [
            'chat_id' => $jsonArray['chatId'] ?? 0,
            'searcher_id' => $jsonArray['searcherId'] ?? 0,
        ];

        $this->replace($parameters);
    }

    public function rules()
    {
        return [
            'chat_id' => 'required|integer',
            'searcher_id' => 'required|integer',
        ];
    }
}
