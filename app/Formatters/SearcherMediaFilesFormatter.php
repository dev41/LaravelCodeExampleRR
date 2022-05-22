<?php

namespace App\Formatters;

use App\Models\SearcherMediaFile;
use App\Repositories\SearcherMediaFileRepository;

class SearcherMediaFilesFormatter extends BaseMediaFilesFormatter
{
    public static function getResolutions(): array
    {
        return SearcherMediaFile::RESOLUTIONS;
    }

    public static function resolveImageFilePath(int $entityId, string $fullName): string
    {
        return SearcherMediaFileRepository::getImageFilePath($entityId, $fullName);
    }
}
