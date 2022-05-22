<?php

namespace App\Services;

use App\Formatters\SuperUserProfileFormatter;
use App\Models\SuperUserProfile;
use App\Models\User;
use App\Repositories\SuperUserProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as IntImage;

class SuperUserProfileService extends Service
{
    public function getById($userId)
    {
        $user = UserRepository::getById($userId);
        $profile = SuperUserProfileRepository::getById($userId);

        return SuperUserProfileFormatter::responseObject($user, $profile);
    }

    public function update(SuperUserProfile $profile, array $data)
    {
        try {
            DB::beginTransaction();

            $profile->fill($data);
            $profile->status = SuperUserProfile::STATUS_FILLED;
            $profile->save();

            /** @var User $user */
            $user = $profile->user()->first();
            $user->fill($data);
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $profile;
    }

    public function getAvatarPath(SuperUserProfile $profile, string $fileName = null): string
    {
        $fileName = $fileName ?? $profile->avatar;
        return 'images/superuser-profile/' . $profile->id . '/' . $fileName;
    }

    public function getAvatarPathByIdAndName(int $profileId, string $fileName): string
    {
        return 'images/superuser-profile/' . $profileId . '/' . $fileName;
    }

    public function getCompanyLogoPath(SuperUserProfile $profile, string $fileName = null): string
    {
        $fileName = $fileName ?? $profile->company_logo;
        return 'images/superuser-profile/' . $profile->id . '/company-logo/' . $fileName;
    }

    public function getCompanyLogoPathByIdAndName(int $profileId, string $fileName): string
    {
        return 'images/superuser-profile/' . $profileId . '/company-logo/' . $fileName;
    }

    public function uploadAvatar(SuperUserProfile $profile)
    {
        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());
        $image = IntImage::make($file->getRealPath());

        $storageType = env('FILES_STORAGE_DRIVER');
        $filePath = $this->getAvatarPath($profile, $pInfo['basename']);

        Storage::disk($storageType)->put($filePath, $image->encode());

        $profile->avatar = $pInfo['basename'];
        $profile->save();

        return $filePath;
    }

    public function uploadCompanyLogo(SuperUserProfile $profile)
    {
        $file = Input::file('file');
        $pInfo = pathinfo($file->getClientOriginalName());
        $image = IntImage::make($file->getRealPath());

        $storageType = env('FILES_STORAGE_DRIVER');
        $filePath = $this->getCompanyLogoPath($profile, $pInfo['basename']);

        Storage::disk($storageType)->put($filePath, $image->encode());

        $profile->company_logo = $pInfo['basename'];
        $profile->save();

        return $filePath;
    }

}
