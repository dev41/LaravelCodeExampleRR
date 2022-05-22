<?php

namespace App\Formatters;

use App\Models\RoomMediaFile;
use App\Repositories\RoomMediaFileRepository;

class RoomMediaFilesFormatter extends BaseMediaFilesFormatter
{
    public static function getResolutions(): array
    {
        return RoomMediaFile::RESOLUTIONS;
    }

    public static function resolveImageFilePath(int $entityId, string $fullName): string
    {
        return RoomMediaFileRepository::getImageFilePath($entityId, $fullName);
    }
}
