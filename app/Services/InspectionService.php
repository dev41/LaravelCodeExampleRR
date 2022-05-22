<?php

namespace App\Services;

use App\Http\Requests\Inspection\CreateRequest;
use App\Models\Inspection;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class InspectionService extends Service
{
    public function create(CreateRequest $createRequest)
    {
        $inspection = Inspection::where([
            'room_id' => $createRequest->roomId,
            'user_id' => $createRequest->userId,
        ])->first();

        if ($inspection) {
            throw new InvalidParameterException('Inspection for this user and this room already exist.');
        }

        $inspection = new Inspection();
        $inspection->room_id = $createRequest->roomId;
        $inspection->user_id = $createRequest->userId;
        $inspection->save();

        return $inspection;
    }
}

