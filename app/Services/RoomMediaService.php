<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Models\Room;
use App\Models\RoomMedia;
use App\Models\RoomMediaFile;
use App\Repositories\RoomMediaFileRepository;
use App\Repositories\RoomMediaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class RoomMediaService extends Service
{

    public function create(Room $room, int $type): RoomMedia
    {
        $count = RoomMedia::where(['room_id' => $room->id, 'type' => $type])->count();

        $maximumNumFiles = $type === RoomMedia::TYPE_PHOTO ? 20 : 1;

        if ($count >= $maximumNumFiles) {
            throw new AccessDeniedException('Maximum number of media files exceeded');
        }

        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());

        try {
            DB::beginTransaction();

            $roomMedia = new RoomMedia();
            $roomMedia->room_id = $room->id;
            $roomMedia->name = $pInfo['filename'];
            $roomMedia->type = $type;
            $roomMedia->save();

            $resolutions = RoomMediaFile::RESOLUTIONS;

            foreach ($resolutions as $resolution => $config) {
                RoomMediaFileRepository::createImage($roomMedia, $file, $resolution);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $roomMedia;
    }

    public function delete(RoomMedia $roomMedia)
    {
        try {
            RoomMediaRepository::removeFilesFromStorage($roomMedia);
            $roomMedia->delete();
        } catch (\Exception $e) {
        }
    }

    public function uploadVideo(Room $room): RoomMedia
    {
        $count = RoomMedia::where(['room_id' => $room->id, 'type' => RoomMedia::TYPE_VIDEO])->count();

        if ($count >= 1) {
            throw new AccessDeniedException('Maximum number of media files exceeded');
        }

        try {
            DB::beginTransaction();

            $file = Input::file('file');

            $roomMedia = new RoomMedia();
            $roomMedia->name = $file->getClientOriginalName();
            $roomMedia->room_id = $room->id;
            $roomMedia->type = RoomMedia::TYPE_VIDEO;
            $roomMedia->save();

            $mediaFile = new RoomMediaFile();
            $mediaFile->room_media_id = $roomMedia->id;
            $mediaFile->name = RoomMediaFileRepository::saveVideoToStorage($room->id, $file);
            $mediaFile->save();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
        }

        return $roomMedia;
    }

}
