<?php

namespace App\Services;

use App\Formatters\ChatAvailableRoomsFormatter;
use App\Libraries\WSProvider;
use App\Models\AgreementFiles;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Offer;
use App\Models\OnboardingAgreements;
use App\Models\OnboardingPaymentRequest;
use App\Models\PersonalInfoRequest;
use App\Models\Room;
use App\Models\UserChat;
use App\Repositories\ChatRepository;
use App\Repositories\MessageRepository;
use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ChatService extends Service
{
    public function create(
        int $ownerId,
        array $userIds,
        string $firstMessage = null,
        string $roomId = null
    ): Chat
    {
        $chat = null;

        try {
            DB::beginTransaction();

            $chat = new Chat();
            $chat->name = $ownerId . '_' . implode('_', $userIds) . '_' . time();
            $chat->room_id = $roomId;
            $chat->save();

            $userChat = new UserChat();
            $userChat->chat_id = $chat->id;
            $userChat->user_id = $ownerId;
            $userChat->user_role = UserChat::USER_ROLE_OWNER;
            $userChat->save();

            foreach ($userIds as $id) {
                $userChat = new UserChat();
                $userChat->chat_id = $chat->id;
                $userChat->user_id = $id;
                $userChat->user_role = UserChat::USER_ROLE_WRITER;
                $userChat->save();
            }

            if ($firstMessage) {
                $message = new Message();
                $message->user_id = $ownerId;
                $message->chat_id = $chat->id;
                $message->type = Message::TYPE_TEXT;
                $message->status = Message::STATUS_CREATED;
                $message->message = $firstMessage;
                $message->ip = $_SERVER['REMOTE_ADDR'] ?? null;
                $message->created_at = new \DateTime();

                $message->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $chat;
    }

    public function reset(Chat $chat)
    {
        try {
            DB::beginTransaction();

            Offer::where(['chat_id' => $chat->id])->delete();
            PersonalInfoRequest::where(['chat_id' => $chat->id])->delete();
            OnboardingPaymentRequest::where(['chat_id' => $chat->id])->delete();

            $agreementFiles = DB::table('agreement_files as af')
                ->select('af.id')
                ->join('onboarding_agreements as oa', 'oa.id', '=', 'af.parent_entity_id')
                ->where(['oa.chat_id' => $chat->id])
                ->get()
                ->all()
            ;
            AgreementFiles::whereIn('id', array_column($agreementFiles, 'id'))->delete();
            OnboardingAgreements::where(['chat_id' => $chat->id])->delete();

            /** @var Message $firstMessage */
            $firstMessage = Message::where(['chat_id' => $chat->id])->orderBy('id')->first();

            Message::where(['chat_id' => $chat->id])
                ->where('id', '<>', $firstMessage->id)
                ->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        $chatData = [
            'messages' => [MessageRepository::getChatMessageData($firstMessage->id)],
            'offer' => null,
            'personalInfo' => null,
            'agreement' => null,
            'onboarding_step' => Chat::ONBOARDING_STEP_OFFER,
        ];

        WSProvider::changeChatState($chat->id, $chatData);
    }

    public function getAvailableRooms(Chat $chat): array
    {
        if (!$this->isChatAvailableToAssignRoom($chat)) {
            throw new InvalidParameterException('This chat cannot be attached to any room.');
        }

        $roomsData = RoomRepository::getAllBySU(auth()->id());

        return ChatAvailableRoomsFormatter::responseObject($roomsData);
    }

    public function assignRoom(Chat $chat, Room $room)
    {
        if (!$this->isChatAvailableToAssignRoom($chat)) {
            throw new InvalidParameterException('This chat cannot be attached to any room.');
        }

        try {
            DB::beginTransaction();

            $this->closeAllOpenRequest($chat->id);
            $this->closeAllOpenSpecialMessages($chat->id);

            $chat->room_id = $room->id;
            $chat->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function closeAllOpenRequest(int $chatId)
    {
        Offer::where([
            'chat_id' => $chatId,
        ])->whereIn('status', [
            Offer::STATUS_SEND,
            Offer::STATUS_CREATE,
        ])->update([
            'status' => Offer::STATUS_INACTIVE,
        ]);

        PersonalInfoRequest::where([
            'chat_id' => $chatId,
        ])->whereIn('status', [
            PersonalInfoRequest::STATUS_CREATED,
        ])->update([
            'status' => PersonalInfoRequest::STATUS_INACTIVE,
        ]);

        OnboardingPaymentRequest::where([
            'chat_id' => $chatId,
        ])->whereIn('status', [
            OnboardingPaymentRequest::STATUS_CREATED,
        ])->update([
            'status' => OnboardingPaymentRequest::STATUS_DECLINED,
        ]);
    }

    public function closeAllOpenSpecialMessages(int $chatId)
    {
        Message::where([
            'chat_id' => $chatId,
            'type' => Message::TYPE_OFFER_SEND,
        ])->update([
            'type' => Message::TYPE_OFFER_CLOSED,
        ]);

        Message::where([
            'chat_id' => $chatId,
            'type' => Message::TYPE_PERSONAL_INFO_REQUEST,
        ])->update([
            'type' => Message::TYPE_PERSONAL_CLOSED,
        ]);

        Message::where([
            'chat_id' => $chatId,
            'type' => Message::TYPE_PAYMENT_REQUEST_SEND,
        ])->update([
            'type' => Message::TYPE_PAYMENT_REQUEST_CANCELED,
        ]);

        Message::where([
            'chat_id' => $chatId,
            'type' => Message::TYPE_ONBOARDING_PAYMENT_REQUEST,
        ])->update([
            'type' => Message::TYPE_ONBOARDING_PAYMENT_DECLINE,
        ]);

        Message::where([
            'chat_id' => $chatId,
        ])->whereIn('type', [
            Message::TYPE_VIEWING_REQUEST_SEND,
            Message::TYPE_VIEWING_SEARCHER_REQUEST_SEND,
        ])->update([
            'type' => Message::TYPE_VIEWING_REQUEST_CANCELED,
        ]);
    }

    public function isChatAvailableToAssignRoom(Chat $chat): bool
    {
        if (!$chat->room_id) {
            return true;
        }

        $room = Room::find($chat->room_id);
        if (!$room) {
            return true;
        }

        $specialMessages = ChatRepository::getRoomLockMessage($chat->id);
        return empty($specialMessages);
    }
}
