<?php

namespace App\Models;

use App\Repositories\PaymentRequestRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class PaymentRequestFiles
 *
 * @property int $id
 * @property int $payment_request_id
 * @property int $type
 * @property string $name
 * @property-read mixed $file_path
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles wherePaymentRequestId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentRequestFiles whereType($value)
 * @property-read mixed $file_name
 * @property-read mixed $file_size
 */
class PaymentRequestFiles extends Model
{
    protected $table = 'payment_request_files';
    public $timestamps = false;

    protected $appends = ['url', 'fileSize'];

    const TYPE_SU_AGREEMENT = 1;
    const TYPE_SEARCHER_INFO = 2;

    public function getFilePathAttribute()
    {
        return PaymentRequestRepository::getFilePath($this->payment_request_id);
    }

    public function getFileNameAttribute()
    {
        return $this->getFilePathAttribute() . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getUrlAttribute()
    {
        return url(PaymentRequestRepository::getFilePath($this->payment_request_id)) . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getFileSizeAttribute()
    {
        $storageType = env('FILES_STORAGE_DRIVER');
        return Storage::disk($storageType)->size($this->getFileNameAttribute());
    }
}
