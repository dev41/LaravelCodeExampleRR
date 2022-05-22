<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomParam
 *
 * @package App
 * @property int $id
 * @property int $profile_id
 * @property int $type
 * @property string $name
 * @property int $position
 * @property int $isCover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SearcherMediaFile[] $files
 * @property-read int|null $files_count
 * @property-read \App\Models\SearcherProfile $profile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia whereIsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearcherMedia whereType($value)
 * @mixin \Eloquent
 */
class SearcherMedia extends Model
{
    const TYPE_PHOTO = 1;
    const TYPE_VIDEO = 3;

    public $timestamps = false;

    protected $fillable = [
        'profile_id',
        'type',
        'name',
        'position',
        'isCover',
    ];

    public function profile()
    {
        return $this->belongsTo(SearcherProfile::class, 'profile_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(SearcherMediaFile::class, 'searcher_media_id', 'id');
    }
}
