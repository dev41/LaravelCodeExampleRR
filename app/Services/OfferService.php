<?php

namespace App\Services;

use App\Formatters\OfferFormatter;
use App\Http\Requests\Offer\UpdateRequest;
use App\Libraries\WSProvider;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Offer;
use App\Models\Room;
use App\Repositories\OfferRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OfferService extends Service
{
    public function createTmp(Room $room): Offer
    {
        if (!$this->roomIsVacant($room)) {
            throw new InvalidParameterException('This room is not vacant');
        }

        $offer = new Offer();

        $offer->creator_id = auth()->id();
        $offer->room_id = $room->id;
        $offer->bills_included = $room->consumablesIncluded === null ||
            $room->consumablesIncluded === Room::CONSUMABLES_NOT_INCLUDED ? Offer::BILLS_INCLUDE_NO : Offer::BILLS_INCLUDE_YES;
        $offer->rent_amount = $room->rent_amount;
        $offer->bills_amount = (int) $room->consumablesValue;

        $offer->save();

        return $offer;
    }

    public function update(Offer $offer, UpdateRequest $request): Offer
    {
        $this->closeAllOpenChatOffers($request->chat_id);

        $offer->fill($request->toArray());
        $offer->save();

        return $offer;
    }

    public function closeAllOpenChatOffers(int $chatId)
    {
        try {
            $messagesQ = Message::where([
                'chat_id' => $chatId,
                'type' => Message::TYPE_OFFER_SEND,
            ]);

            $messages = $messagesQ->get()->all();

            $messagesQ->update([
                'type' => Message::TYPE_OFFER_CLOSED,
            ]);

            /** @var Message $message */
            foreach ($messages as $message) {
                WSProvider::changeMessageType($message->chat_id, $message->id, Message::TYPE_OFFER_CLOSED);
            }
        } catch (\Exception $e) {
        }

        try {
            Offer::where([
                'chat_id' => $chatId,
                'status' => Offer::STATUS_CREATE,
            ])->update([
                'status' => Offer::STATUS_INACTIVE,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function changeStatus(Offer $offer, int $status): Offer
    {
        if ($status === Offer::STATUS_ACCEPTED && !$offer->isAcceptable()) {
            throw new InvalidParameterException('Offer is`t acceptable.');
        }

        $offer->status = $status;
        $offer->save();

        if ($status === Offer::STATUS_ACCEPTED) {
            $allRoomOffers = Offer::where(['room_id' => $offer->room_id])
                ->whereIn('status', [Offer::STATUS_CREATE, Offer::STATUS_SEND]);

            $offerChatIds = array_filter($allRoomOffers->pluck('chat_id')->all());

            if ($offerChatIds) {
                Chat::whereIn('id', $offerChatIds)->update(['room_id' => null]);
                Message::whereIn('chat_id', $offerChatIds)
                    ->where(['type' => Message::TYPE_OFFER_SEND])
                    ->update(['type' => Message::TYPE_OFFER_DECLINED]);
                $allRoomOffers->delete();
            }
        }

        return $offer;
    }

    public function getFormattedOffer(Offer $offer)
    {
        $offerData = OfferRepository::getById($offer->id);
        return OfferFormatter::responseObject($offerData);
    }

    public function roomIsVacant(Room $room): bool
    {
        $offers = $room->offers()->where(['offer.status' => Offer::STATUS_ACCEPTED])->get()->all();
        return empty($offers);
    }
}
