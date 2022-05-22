<?php

namespace App\Repositories;

use App\Formatters\RoomMediaFilesFormatter;
use App\Models\RoomMedia;
use App\Models\RoomMediaFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomMediaRepository extends BaseMediaRepository
{
    public static function getByRoomId(int $roomId): array
    {
        $medias = DB::table('room_media as rm')->select([
                'rm.id as id',
                'rm.type as type',
                'rm.room_id as entity_id',
                'rm.name as original_name',
                'rmf.name as file_name',
                'rmf.resolution as resolution',
            ])
            ->join('room_media_files as rmf', 'rm.id', '=', 'rmf.room_media_id')
            ->where(['rm.room_id' => $roomId])
            ->whereIn('rm.type', [RoomMedia::TYPE_PHOTO, RoomMedia::TYPE_FLOOR_PLAN, RoomMedia::TYPE_VIDEO])
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc');

        $medias = $medias->get();

        $photos = [];
        $floorplan = [];
        $video = false;

        foreach ($medias as $media) {
            switch ($media->type) {
                case RoomMedia::TYPE_PHOTO:
                    $photos[] = $media;
                    break;
                case RoomMedia::TYPE_FLOOR_PLAN:
                    $floorplan[] = $media;
                    break;
                case RoomMedia::TYPE_VIDEO:
                    $video = [
                        'id' => $media->id,
                        'name' => $media->original_name,
                        'url' => RoomMediaFileRepository::getVideoFilePath($roomId, $media->file_name),
                    ];
                    break;
            }
        }

        $photos = array_values(RoomMediaFilesFormatter::format($photos));
        $floorplan = array_values(RoomMediaFilesFormatter::format($floorplan));

        return [
            'photos' => $photos ?: [],
            'floorplan' => empty($floorplan) ? false : reset($floorplan),
            'video' => $video,
        ];
    }

    public static function getByRoomMediaId(int $mediaId): array
    {
        $roomImages = DB::table('room_media as rm')->select([
                'rm.id as id',
                'rm.room_id as entity_id',
                'rm.name as original_name',
                'rms.name as file_name',
                'rms.resolution as resolution',
            ])
            ->leftJoin('room_media_files as rms', 'rm.id', '=', 'rms.room_media_id')
            ->where(['rms.room_media_id' => $mediaId]);

        $roomImages = $roomImages->get();

        return array_values(RoomMediaFilesFormatter::format($roomImages));
    }

    public static function removeFilesFromStorage(RoomMedia $roomMedia)
    {
        $mediaFiles = $roomMedia->files()->get();

        /** @var RoomMediaFile $mediaFile */
        foreach ($mediaFiles as $mediaFile) {
            $mediaFilePath = RoomMediaFileRepository::getImageFilePath($roomMedia->room_id, $mediaFile->name);

            $storageType = env('FILES_STORAGE_DRIVER');
            Storage::disk($storageType)->delete($mediaFilePath);
        }
    }

    public static function removeFilesFromStorageByRooms(int $roomId)
    {
        $roomMediaFiles = DB::table('room_media_files as rmf')
            ->select([
                'rmf.name as name',
                'rm.type as type',
            ])
            ->leftJoin('room_media as rm', 'rmf.room_media_id', '=', 'rm.id')
            ->where(['rm.room_id' => $roomId])
            ->get();

        foreach ($roomMediaFiles as $mediaFile) {

            if ($mediaFile->type === RoomMedia::TYPE_VIDEO) {
                $mediaFilePath = RoomMediaFileRepository::getVideoFilePath($roomId, $mediaFile->name);
            } else {
                $mediaFilePath = RoomMediaFileRepository::getImageFilePath($roomId, $mediaFile->name);
            }

            $storageType = env('FILES_STORAGE_DRIVER');
            Storage::disk($storageType)->delete($mediaFilePath);
        }
    }

    public static function updateImagePositions(array $newPositions)
    {
        if (empty($newPositions)) {
            return;
        }

        $ids = array_flip($newPositions);
        $images = RoomMedia::whereIn('id', $newPositions)->get();

        foreach ($images as $image) {
            $image->position = $ids[$image->id];
            $image->save();
        }
    }

}
