<?php

namespace App\Repositories;

use App\Models\RoomMedia;
use App\Models\RoomMediaFile;
use Illuminate\Http\UploadedFile;

class RoomMediaFileRepository extends BaseMediaFileRepository
{
    public static function getResolutions(): array
    {
        return RoomMediaFile::RESOLUTIONS;
    }

    public static function getImagesSubDir(): string
    {
        return 'room';
    }

    public static function getVideosSubDir(): string
    {
        return 'room';
    }

    public static function createImage(RoomMedia $roomMedia, UploadedFile $file, int $resolution): RoomMediaFile
    {
        $mediaFile = new RoomMediaFile();
        $mediaFile->room_media_id = $roomMedia->id;
        $mediaFile->resolution = $resolution;
        $mediaFile->name = self::saveImageFileToStorage($roomMedia->room_id, $file, $resolution);
        $mediaFile->save();

        return $mediaFile;
    }
}
