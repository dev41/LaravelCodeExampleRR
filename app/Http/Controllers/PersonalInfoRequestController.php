<?php

namespace App\Http\Controllers;

use App\Formatters\PersonalInfoRequestFormatter;
use App\Http\Requests\PersonalInfoRequest\CreateRequest;
use App\Http\Requests\PersonalInfoRequest\UpdateRequest;
use App\Models\PersonalInfoRequest;
use App\Models\PersonalInfoRequestFile;
use App\Repositories\PersonalInfoRequestRepository;
use App\Services\PersonalInfoRequestService;

class PersonalInfoRequestController extends Controller
{
    public function createTmp(CreateRequest $createRequest, PersonalInfoRequestService $personalInfoRequestService)
    {
        $request = $personalInfoRequestService->createTmp($createRequest);
        $requestData = PersonalInfoRequestRepository::getById($request->id);

        return response()->json(PersonalInfoRequestFormatter::responseObject($requestData));
    }

    public function decline(PersonalInfoRequest $request, PersonalInfoRequestService $personalInfoRequestService)
    {
        $request = $personalInfoRequestService->decline($request);
        $requestData = PersonalInfoRequestRepository::getById($request->id);

        return response()->json(PersonalInfoRequestFormatter::responseObject($requestData));
    }

    public function update(PersonalInfoRequest $request, UpdateRequest $updateRequest, PersonalInfoRequestService $personalInfoRequestService)
    {
        $request = $personalInfoRequestService->update($request, $updateRequest);
        $requestData = PersonalInfoRequestRepository::getById($request->id);

        return response()->json(PersonalInfoRequestFormatter::responseObject($requestData));
    }

    public function getById(PersonalInfoRequest $request)
    {
        $requestData = PersonalInfoRequestRepository::getById($request->id);
        return response()->json(PersonalInfoRequestFormatter::responseObject($requestData));
    }

    public function attachFile(PersonalInfoRequest $request, PersonalInfoRequestService $personalInfoRequestService)
    {
        $personalInfoRequestFile = $personalInfoRequestService->attachFile($request);

        return response()->json([
            'file' => $personalInfoRequestFile,
        ]);
    }

    public function detachFile(PersonalInfoRequestFile $file, PersonalInfoRequestService $personalInfoRequestService)
    {
        $personalInfoRequestFile = $personalInfoRequestService->detachFile($file);

        return response()->json([
            'success' => true,
        ]);
    }
}
