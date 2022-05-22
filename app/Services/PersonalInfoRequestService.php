<?php

namespace App\Services;

use App\Http\Requests\PersonalInfoRequest\CreateRequest;
use App\Http\Requests\PersonalInfoRequest\UpdateRequest;
use App\Libraries\WSProvider;
use App\Models\Message;
use App\Models\PersonalInfoRequest;
use App\Models\PersonalInfoRequestFile;
use App\Models\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class PersonalInfoRequestService extends Service
{
    public function createTmp(CreateRequest $request)
    {
        $this->closeAllPreviousRequests($request->chat_id);

        $searcher = User::find($request->searcher_id);

        $personalInfoRequest = new PersonalInfoRequest();
        $personalInfoRequest->creator_id = auth()->id();
        $personalInfoRequest->searcher_id = $searcher->id;
        $personalInfoRequest->chat_id = $request->chat_id;
        $personalInfoRequest->first_name = $searcher->first_name;
        $personalInfoRequest->last_name = $searcher->last_name;
        $personalInfoRequest->phone = $searcher->phone_number;
        $personalInfoRequest->email = $searcher->email;

        $personalInfoRequest->save();

        return $personalInfoRequest;
    }

    public function closeAllPreviousRequests(int $chatId)
    {
        PersonalInfoRequest::where([
            'chat_id' => $chatId,
        ])->update([
            'status' => PersonalInfoRequest::STATUS_INACTIVE,
        ]);

        $messagesQ = Message::where([
            'chat_id' => $chatId,
            'type' => Message::TYPE_PERSONAL_INFO_REQUEST,
        ]);

        $messages = $messagesQ->get()->all();

        $messagesQ->update([
            'type' => Message::TYPE_PERSONAL_CLOSED,
        ]);

        /** @var Message $message */
        foreach ($messages as $message) {
            WSProvider::changeMessageType($chatId, $message->id, Message::TYPE_PERSONAL_CLOSED);
        }
    }

    public function update(PersonalInfoRequest $request, UpdateRequest $updateRequest)
    {
        $request->fill($updateRequest->toArray());
        $request->status = PersonalInfoRequest::STATUS_ACCEPTED;
        $request->save();

        return $request;
    }

    public function decline(PersonalInfoRequest $request)
    {
        $request->status = PersonalInfoRequest::STATUS_DECLINED;
        $request->save();

        return $request;
    }

    public function attachFile(PersonalInfoRequest $request): PersonalInfoRequestFile
    {
        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());

        $storageType = env('FILES_STORAGE_DRIVER');

        $pirFile = new PersonalInfoRequestFile();
        $pirFile->personal_info_request_id = $request->id;
        $pirFile->name = $pInfo['basename'];

        request()->file('file')->storeAs($pirFile->getFilePathAttribute(), $pInfo['basename'], $storageType);

        $pirFile->save();

        return $pirFile;
    }

    public function detachFile(PersonalInfoRequestFile $file)
    {
        $filePath = $file->getFileNameAttribute();

        $storageType = env('FILES_STORAGE_DRIVER');
        Storage::disk($storageType)->delete($filePath);

        $file->delete();

        return true;
    }

}
