<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomMediaFile
 *
 * @package App\Models
 * @property integer $id
 * @property integer $searcher_media_id
 * @property integer $resolution
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMediaFile whereSearcherMediaId($value)
 * @mixin \Eloquent
 */
class SearcherMediaFile extends Model
{
    public $timestamps = false;

    const RESOLUTION_DESKTOP = 1;
    const RESOLUTION_TABLET = 2;
    const RESOLUTION_THUMBNAIL = 3;

    const RESOLUTIONS = [
        self::RESOLUTION_DESKTOP => ['prefix' => '1280_405', 'w' => 1280, 'h' => 405],
        self::RESOLUTION_TABLET => ['prefix' => '770_340', 'w' => 770, 'h' => 340],
        self::RESOLUTION_THUMBNAIL => ['prefix' => '180_100', 'w' => 180, 'h' => 100],
    ];

    protected $fillable = [
        'searcher_media_id',
        'resolution',
        'name',
    ];
}
