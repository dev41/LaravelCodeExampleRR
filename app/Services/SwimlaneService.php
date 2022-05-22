<?php

namespace App\Services;

use App\Models\PaymentRequest;
use App\Repositories\PaymentRequestRepository;
use App\Repositories\RoomMediaFileRepository;
use App\Repositories\SwimlaneRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class SwimlaneService extends Service
{
    public function getAll()
    {
        $rooms = SwimlaneRepository::getAllRooms(auth()->id());

        if (empty($rooms)) {
            return [];
        }

        $roomRequests = $this->getPaymentRequestsByRoomIds(array_column($rooms, 'roomId'));

        foreach ($rooms as $room) {
            if ($room->roomCover) {
                $coverFileName = explode(SwimlaneRepository::ROOM_MEDIA_FILENAME_SEPARATOR, $room->roomCover);
                $coverFileName = reset($coverFileName);

                $room->roomCover = RoomMediaFileRepository::getImageFilePath($room->roomId, $coverFileName);
            }

            $room->payment = $roomRequests[$room->roomId] ?? null;

            $room->swimlane = !$room->payment ? SwimlaneRepository::STATUS_VIEWING : SwimlaneRepository::STATUS_PAYMENT;
        }


        return $rooms;
    }

    public function getPaymentRequestsByRoomIds(array $roomIds): array
    {
        $paymentRequests = SwimlaneRepository::getPaymentRequestsByRooms($roomIds);

        $roomRequests = [];

        foreach ($paymentRequests as $request) {

            if (!isset($roomRequests[$request->room_id])) {

                $request->medias = $request->medias ? explode(SwimlaneRepository::ROOM_MEDIA_FILENAME_SEPARATOR, $request->medias) : [];

                foreach ($request->medias as $key => $media) {
                    $request->medias[$key] = url(PaymentRequestRepository::getFilePath($request->id)) . '/' . $media;
                }

                $roomRequests[$request->room_id] = [
                    'expiration' => $request->expiration,
                    'moveInDate' => $request->move_in_date,
                    'rent' => null,
                    'bond' => null,
                    'deposit' => null,
                    'user' => [
                        'id' => $request->user_id,
                        'firstName' => $request->user_first_name,
                        'lastName' => $request->user_last_name,
                        'middleName' => $request->user_middle_name,
                        'birthDay' => $request->user_birthday,
                        'media' => $request->medias,
                    ],
                ];
            }

            $prPrice = [
                'value' => $request->price,
                'status' => $request->status,
            ];

            switch ($request->price_type) {
                case PaymentRequest::TYPE_DEPOSIT:
                    $roomRequests[$request->room_id]['deposit'] = $prPrice;
                    break;
                case PaymentRequest::TYPE_INITIAL_RENT:
                    $roomRequests[$request->room_id]['rent'] = $prPrice;
                    break;
                case PaymentRequest::TYPE_BOND:
                    $roomRequests[$request->room_id]['bond'] = $prPrice;
                    break;
                default:
                    throw new InvalidParameterException('Invalid payment request type.');
            }
        }

        return $roomRequests;
    }
}
