<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Models\SearcherMedia;
use App\Models\SearcherMediaFile;
use App\Models\SearcherProfile;
use App\Repositories\SearcherMediaFileRepository;
use App\Repositories\SearcherMediaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SearcherMediaService extends Service
{
    public function create(SearcherProfile $profile, int $type): SearcherMedia
    {
        $count = SearcherMedia::where(['profile_id' => $profile->id, 'type' => $type])->count();

        $maximumNumFiles = $type === SearcherMedia::TYPE_PHOTO ? 20 : 1;

        if ($count >= $maximumNumFiles) {
            throw new AccessDeniedException('Maximum number of media files exceeded');
        }

        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());

        $profileMedia = new SearcherMedia();
        $profileMedia->profile_id = $profile->id;
        $profileMedia->name = $pInfo['filename'];
        $profileMedia->type = $type;
        $profileMedia->save();

        $resolutions = SearcherMediaFile::RESOLUTIONS;
        foreach ($resolutions as $resolution => $config) {
            SearcherMediaFileRepository::createImage($profileMedia, $file, $resolution);
        }

        return $profileMedia;
    }

    public function delete(SearcherMedia $media)
    {
        try {
            SearcherMediaRepository::removeFilesFromStorage($media);
            $media->delete();
        } catch (\Exception $e) {
        }
    }

    public function uploadAvatar(SearcherProfile $profile): string
    {
        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());
        $image = Image::make($file->getRealPath());

        $storageType = env('FILES_STORAGE_DRIVER');
        $filePath = SearcherMediaFileRepository::getImageFilePath($profile->id, $pInfo['basename']);

        Storage::disk($storageType)->put($filePath, $image->encode());

        $profile->avatar = $pInfo['basename'];
        $profile->save();

        return $filePath;
    }

    public function uploadVideo(SearcherProfile $profile): SearcherMedia
    {
        $count = SearcherMedia::where(['profile_id' => $profile->id, 'type' => SearcherMedia::TYPE_VIDEO])->count();

        if ($count >= 1) {
            throw new AccessDeniedException('Maximum number of media files exceeded');
        }

        try {
            DB::beginTransaction();

            $file = Input::file('file');

            $media = new SearcherMedia();
            $media->name = $file->getClientOriginalName();
            $media->profile_id = $profile->id;
            $media->type = SearcherMedia::TYPE_VIDEO;
            $media->save();

            $mediaFile = new SearcherMediaFile();
            $mediaFile->searcher_media_id = $media->id;
            $mediaFile->name = SearcherMediaFileRepository::saveVideoToStorage($profile->id, $file);
            $mediaFile->save();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
        }

        return $media;
    }

}
