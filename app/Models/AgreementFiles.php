<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

/**
 * Class AgreementFiles
 * @package App\Models
 *
 * @property int $id
 * @property int $creator_id
 * @property int $parent_entity_id
 * @property int $scope_id
 * @property int $type
 * @property string $name
 *
 */
class AgreementFiles extends BaseModel
{
    protected $table = 'agreement_files';

    const TYPE_AGREEMENT_ORIGIN = 1;
    const TYPE_AGREEMENT_SU_SIGNED = 2;
    const TYPE_AGREEMENT_RS_SIGNED = 3;
    const TYPE_HOUSE_RULES_ORIGIN = 10;
    const TYPE_HOUSE_RULES_SU_SIGNED = 11;
    const TYPE_HOUSE_RULES_RS_SIGNED = 12;

    const FILES_SUB_DIR = 'agreement-files';

    protected $appends = ['url', 'fileSize'];

    public function getFilePathAttribute(): string
    {
        return 'files' . DIRECTORY_SEPARATOR . self::FILES_SUB_DIR
            . DIRECTORY_SEPARATOR . $this->id;
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
