<?php

namespace App\Repositories;

use App\Formatters\SearcherMediaFilesFormatter;
use App\Models\SearcherMedia;
use App\Models\SearcherMediaFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SearcherMediaRepository extends BaseMediaRepository
{
    public static function getByProfileId(int $profileId): array
    {
        $medias = DB::table('searcher_media as sm')->select([
                'sm.id as id',
                'sm.type as type',
                'sm.profile_id as entity_id',
                'sm.name as original_name',
                'smf.name as file_name',
                'smf.resolution as resolution',
            ])
            ->leftJoin('searcher_media_files as smf', 'sm.id', '=', 'smf.searcher_media_id')
            ->where(['sm.profile_id' => $profileId])
            ->whereIn('sm.type', [SearcherMedia::TYPE_PHOTO, SearcherMedia::TYPE_VIDEO])
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc');

        $medias = $medias->get();

        $photos = [];
        $video = false;

        foreach ($medias as $media) {
            switch ($media->type) {
                case SearcherMedia::TYPE_PHOTO:
                    $photos[] = $media;
                    break;
                case SearcherMedia::TYPE_VIDEO:
                    $video = [
                        'id' => $media->id,
                        'name' => $media->original_name,
                        'url' => SearcherMediaFileRepository::getVideoFilePath($profileId, $media->file_name),
                    ];
                    break;
            }
        }

        $photos = array_values(SearcherMediaFilesFormatter::format($photos));

        return [
            'photos' => $photos,
            'video' => $video,
        ];
    }

    public static function getBySearcherMediaId(int $mediaId): array
    {
        $searcherImages = DB::table('searcher_media as sm')->select([
                'sm.id as id',
                'sm.profile_id as entity_id',
                'sm.name as original_name',
                'smf.name as file_name',
                'smf.resolution as resolution',
            ])
            ->leftJoin('searcher_media_files as smf', 'sm.id', '=', 'smf.searcher_media_id')
            ->where(['smf.searcher_media_id' => $mediaId]);

        $searcherImages = $searcherImages->get();

        return array_values(SearcherMediaFilesFormatter::format($searcherImages));
    }

    public static function removeFilesFromStorage(SearcherMedia $media)
    {
        $mediaFiles = $media->files()->get();

        /** @var SearcherMediaFile $mediaFile */
        foreach ($mediaFiles as $mediaFile) {
            $mediaFilePath = SearcherMediaFileRepository::getImageFilePath($media->profile_id, $mediaFile->name);

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
        $images = SearcherMedia::whereIn('id', $newPositions)->get();

        foreach ($images as $image) {
            $image->position = $ids[$image->id];
            $image->save();
        }
    }

}
