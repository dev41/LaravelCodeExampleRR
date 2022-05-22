<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

/**
 * Class OnboardingAgreement
 * @package App\Models
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $type
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 */
class OnboardingAgreement extends BaseModel
{
    protected $table = 'onboarding_agreement';

    const FILES_SUB_DIR = 'onboarding-agreement';

    const TYPE_ORIGINAL = 1;
    const TYPE_SIGNED = 2;

    protected $appends = ['url', 'fileSize'];

    public function getFilePathAttribute(): string
    {
        return 'files' . DIRECTORY_SEPARATOR . self::FILES_SUB_DIR
            . DIRECTORY_SEPARATOR . $this->request_id;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function request()
    {
        return $this->hasOne(OnboardingPaymentRequest::class, 'id', 'request_id');
    }
}
