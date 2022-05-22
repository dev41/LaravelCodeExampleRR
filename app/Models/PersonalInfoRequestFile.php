<?php

namespace App\Models;

use App\Repositories\PersonalInfoRequestRepository;
use Illuminate\Support\Facades\Storage;

/**
 * Class PersonalInfoRequestFile
 * @package App\Models
 *
 * @property int $id
 * @property int $personal_info_request_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 */
class PersonalInfoRequestFile extends BaseModel
{
    protected $table = 'personal_info_request_file';

    protected $appends = ['url', 'fileSize'];

    public function getFilePathAttribute()
    {
        return PersonalInfoRequestRepository::getFilePath($this->personal_info_request_id);
    }

    public function getFileNameAttribute()
    {
        return $this->getFilePathAttribute() . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getUrlAttribute()
    {
        return url($this->getFilePathAttribute()) . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getFileSizeAttribute()
    {
        $storageType = env('FILES_STORAGE_DRIVER');
        return Storage::disk($storageType)->size($this->getFileNameAttribute());
    }
}
