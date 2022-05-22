<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest\CreateTmpRequest;
use App\Http\Requests\PaymentRequest\RSUpdateRequest;
use App\Http\Requests\PaymentRequest\SUUpdateRequest;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestFiles;
use App\Models\Room;
use App\Models\SuperUserProfile;
use App\Models\User;
use App\Services\PaymentRequestService;
use App\Repositories\SearcherMediaFileRepository;
use App\Services\SwimlaneService;
use Firebase\JWT\ExpiredException;
use Symfony\Component\HttpFoundation\Response;

class PaymentRequestController extends Controller
{
    public function viewForRs(PaymentRequest $paymentRequest, PaymentRequestService $paymentRequestService)
    {
        try {
            $info = $paymentRequestService->viewForRs($paymentRequest);
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'expired'], Response::HTTP_FORBIDDEN);
        }

        return response()->json($info);
    }

    public function createTmpRequest(PaymentRequestService $paymentRequestService, CreateTmpRequest $request)
    {
        $paymentRequest = $paymentRequestService->createTmpRequest($request);

        $renter = null;
        if ($request->userId) {
            $renter = User::find($request->userId);
            $suProfile = SuperUserProfile::find(auth()->id());
            $renter = [
                'id' => $renter->id,
                'firstName' => $renter->first_name,
                'lastName' => $renter->last_name,
                'fullName' => $renter->first_name . ' ' . $renter->last_name,
                'avatar' => $suProfile->avatar ? '/' . SearcherMediaFileRepository::getImageFilePath($suProfile->id, $suProfile->avatar) : '',
            ];
        }

        return response()->json([
            'id' => $paymentRequest->id,
            'renter' => $renter,
        ], Response::HTTP_CREATED);
    }

    public function superuserUpdate(
        PaymentRequest $paymentRequest,
        SUUpdateRequest $request,
        PaymentRequestService $paymentRequestService,
        SwimlaneService $swimlaneService
    ) {
        $paymentRequest = $paymentRequestService->superuserUpdate($paymentRequest, $request);

        $formattedPR = $swimlaneService->getPaymentRequestsByRoomIds([$paymentRequest->room_id]);

        return response()->json(reset($formattedPR), Response::HTTP_OK);
    }

    public function searcherUpdate(PaymentRequest $paymentRequest, RSUpdateRequest $request, PaymentRequestService $paymentRequestService)
    {
        $paymentRequestRSProfile = $paymentRequestService->searcherUpdate($paymentRequest, $request);

        return response()->json([
            'paymentRequestSearcherProfile' => $paymentRequestRSProfile,
            'paymentRequest' => $paymentRequest,
        ], Response::HTTP_OK);
    }

    public function attachFile(PaymentRequest $paymentRequest, PaymentRequestService $paymentRequestService)
    {
        $type = request()->post('type');
        $paymentRequestFile = $paymentRequestService->attachFile($paymentRequest, $type);

        return response()->json([
            'file' => $paymentRequestFile,
        ]);
    }

    public function detachFile(PaymentRequestFiles $paymentRequestFile, PaymentRequestService $paymentRequestService)
    {
        $paymentRequestFile = $paymentRequestService->detachFile($paymentRequestFile);

        return response()->json([
            'success' => true,
        ]);
    }

    public function getUserList(Room $room, PaymentRequestService $paymentRequestService)
    {
        $users = $paymentRequestService->getAvailableUserList($room->id);

        return response()->json([
            'users' => $users,
        ]);
    }
}
