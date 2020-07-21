<?php

namespace App\Models\Events;

use App\Models\Traits\FullTextSearchTrait;
use App\Models\Traits\ThumbTrait;
use App\User;
use Carbon\Carbon;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Query\Builder;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use CyrildeWit\EloquentViewable\Viewable;
use Sentinel;
use File;

/**
 * Class Event
 * @package App\Models\Events
 */
class Event extends Model implements Seoable
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use ThumbTrait;
    use SeoableTrait;
    use FullTextSearchTrait;
    use Viewable;
    use ElasticquentTrait;

    const TYPE_OPENED = 'opened';
    const TYPE_CLOSED = 'closed';

    /**
     * @var string
     */
    public $table = 'events';

    protected $appends = ['starts', 'map'];

    /**
     * @var array
     */
    public $fillable = [
        'title',
        'category_id',
        'type',
        'description',
        'price',
        'starts_at',
        'ends_at',
        'address',
        'map_lat' => 'string',
        'map_lng' => 'string',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'address' => 'string'
    ];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = ['starts_at', 'ends_at'];

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'description',
        'address'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['user_id', 'title']
            ]
        ];
    }

    public function seoable()
    {
        $this->seo()->setTitleRaw($this->title)->setKeywordsRaw('')->setDescriptionRaw('');
    }

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'events';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'type' => [
            'type' => 'text'
        ],
        'slug' => [
            'type' => 'text'
        ],
        'title' => [
            'type' => 'text'
        ],
        'description' => [
            'type' => 'text'
        ],
        'map' => [
            'type' => 'geo_point'
        ],
        'starts' => [
            'type' => 'date'
        ],
        'created_at' => [
            'type' => 'text',
            'index' => false
        ],
        'updated_at' => [
            'type' => 'text',
            'index' => false
        ],
        'deleted_at' => [
            'type' => 'text',
            'index' => false
        ],
    );

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_OPENED => mb_convert_case(self::TYPE_OPENED, MB_CASE_TITLE),
            self::TYPE_CLOSED => mb_convert_case(self::TYPE_CLOSED, MB_CASE_TITLE)
        ];
    }

    /**
     * Prepare location for Elasticsearch
     *
     * @return array|null
     */
    public function getMapAttribute()
    {
        $map_lat = $this->map_lat;
        $map_lng = $this->map_lng;
        if (!is_null($map_lat) && !is_null($map_lng)) {
            return [
                'lat' => $map_lat,
                'lon' => $map_lng
            ];
        }

        return null;
    }

    /**
     * @return int
     */
    public function getStartsAttribute()
    {
        return $this->starts_at->format('c');
    }

    /**
     * @return mixed
     */
    public function getMetaAttribute() {
        return $this->getSeoData();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
        return $this->hasOne(\App\File::class, 'id', 'image_id');
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', date('Y-m-d 00:00:00'));
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeMy($query, $related_id = 0)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('created_at', 'desc')
            ->where('user_id', $user->getUserId())
            ->where('related_member_id', $related_id);
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeMyToday($query, $related_id = 0)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('starts_at', 'asc')
            ->where('user_id', $user->getUserId())
            ->where('related_member_id', $related_id)
            ->where('starts_at', '>=', (new Carbon())
                ->format('Y-m-d 00:00:00'))->where('starts_at', '<=', (new Carbon())
                ->format('Y-m-d 23:59:59'));
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->description = strip_tags($this->description, str_replace('<hr>', '', config('app.allowable_tags')));

        return parent::save($options);
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return (bool)$this->image_id;
    }

    /**
     * @param $size
     * @return bool|string
     */
    public function getThumb($size)
    {
        $url = null;
        if ($this->hasImage()) {
            $url = $this->image->getThumb($size);
        }

        if (!$url) {
            $url = str_replace('{size}', $size, config('app.placeholder_url'));
        }

        return $url;
    }
}
