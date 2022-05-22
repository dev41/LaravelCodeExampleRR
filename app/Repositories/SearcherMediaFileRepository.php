<?php

namespace App\Repositories;

use App\Models\SearcherMedia;
use App\Models\SearcherMediaFile;
use Illuminate\Http\UploadedFile;

class SearcherMediaFileRepository extends BaseMediaFileRepository
{
    public static function getResolutions(): array
    {
        return SearcherMediaFile::RESOLUTIONS;
    }

    public static function getImagesSubDir(): string
    {
        return 'searcher-profile';
    }

    public static function getVideosSubDir(): string
    {
        return 'searcher-profile';
    }

    public static function createImage(SearcherMedia $searcherMedia, UploadedFile $file, int $resolution = null): SearcherMediaFile
    {
        $mediaFile = new SearcherMediaFile();
        $mediaFile->searcher_media_id = $searcherMedia->id;
        $mediaFile->resolution = $resolution;
        $mediaFile->name = self::saveImageFileToStorage($searcherMedia->profile_id, $file, $resolution);
        $mediaFile->save();

        return $mediaFile;
    }
}
