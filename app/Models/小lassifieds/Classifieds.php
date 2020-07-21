<?php

namespace App\Models\Classifieds;

use App\File;
use App\Jobs\Index\ClassifiedsRemove;
use App\Models\Traits\FullTextSearchTrait;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselManufacturer;
use App\User;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use CyrildeWit\EloquentViewable\Viewable;
use Sentinel;
use Cache;

/**
 * Class Classifieds
 * @package App\Models\Classifieds
 */
class Classifieds extends Model implements Seoable
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;
    use FullTextSearchTrait;
    use ElasticquentTrait;
    use Viewable;

    const TYPE_BOAT = 'boat';
    const TYPE_PART = 'part';
    const TYPE_ACCESSORY = 'accessory';

    const STATE_NEW = 'new';
    const STATE_USED = 'used';

    public $table = 'classifieds';

    protected $dates = ['expired_at', 'deleted_at'];

    protected $appends = ['category_slug', 'posted', 'map', 'manufacturer_title'];

    public $fillable = [
        'title',
        'type',
        'state',
        'category_id',
        'manufacturer_id',
        'description',
        'price',
        'address',
        'year',
        'length',
        'part_no',
        'vessel_id',
        'refresh_email'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'type' => 'string',
        'state' => 'string',
        'category_id' => 'integer',
        'manufacturer_id' => 'integer',
        'description' => 'string',
        'address' => 'string',
        'map_lat' => 'string',
        'map_lng' => 'string',
        'year' => 'string',
        'length' => 'string',
        'refresh_email' => 'string'
    ];

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
        return 'classifieds';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'category_slug' => [
            'type' => 'text'
        ],
        'slug' => [
            'type' => 'text'
        ],
        'title' => [
            'type' => 'text',
            'fielddata' => true
        ],
        'description' => [
            'type' => 'text'
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
        'posted' => [
            'type' => 'date',
        ],
        'length' => [
            'type' => 'integer',
        ],
        'year' => [
            'type' => 'integer',
        ],
        'part_no' => [
            'type' => 'text',
        ],
        'price' => [
            'type' => 'double',
        ],
        'map' => [
            'type' => 'geo_point'
        ],
    );

    /**
     * @return int
     */
    public function getPostedAttribute()
    {
        return $this->created_at->format('c');
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
     * @return string
     */
    public function getCategorySlugAttribute()
    {
        return $this->category->slug;
    }

    /**
     * @return mixed
     */
    public function getMetaAttribute()
    {
        return $this->getSeoData();
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return Cache::get('classifieds_types', function () {
            return [
                self::TYPE_BOAT => mb_convert_case(self::TYPE_BOAT, MB_CASE_TITLE),
                self::TYPE_PART => mb_convert_case(self::TYPE_PART, MB_CASE_TITLE),
                self::TYPE_ACCESSORY => mb_convert_case(self::TYPE_ACCESSORY, MB_CASE_TITLE)
            ];
        });
    }

    /**
     * @return string|null
     */
    public function getTypeLabelAttribute()
    {
        return self::getTypes()[$this->type] ?? null;
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return $this->address;
    }

    /**
     * @return array
     */
    public static function getStates()
    {
        return Cache::get('classifieds_states', function () {
            return [
                self::STATE_NEW => mb_convert_case(self::STATE_NEW, MB_CASE_TITLE),
                self::STATE_USED => mb_convert_case(self::STATE_USED, MB_CASE_TITLE)
            ];
        });
    }

    /**
     * @return string|null
     */
    public function getStateLabelAttribute()
    {
        return self::getStates()[$this->state] ?? null;
    }

    /**
     * @return string|null
     */
    public function getPriceLabelAttribute()
    {
        $price = round($this->price);
        $formatted = "$" . number_format(sprintf('%d', preg_replace("/[^0-9.]/", "", $price)), 0);
        return $price < 0 ? "({$formatted})" : "{$formatted}";
    }

    /**
     * @return int
     */
    public function getRefreshEmailAttribute()
    {
        return $this->getAttributeFromArray('refresh_email') ? $this->getAttributeFromArray('refresh_email') : $this->user->email;
    }

    /**
     * @return int
     */
    public function getManufacturerTitleAttribute()
    {
        if($this->manufacturer) {
            return $this->manufacturer->title;
        } else {
            return '';
        }
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
        return $this->belongsTo(ClassifiedsCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(ClassifiedsManufacturer::class, 'manufacturer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function images()
    {
        return $this->hasMany(ClassifiedsImages::class, 'classified_id')->sorted();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thread()
    {
        return $this->hasOne(ClassifiedsMessenger::class, 'classified_id', 'id');
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        if ($this->images->count()) {
            return $this->images->first()->getThumb($size);
        }

        return str_replace('{size}', $size, config('app.placeholder_url'));
    }

    public function scopePublished($query)
    {
        return $query->where($this->getTable() . '.status', 'approved');
    }

    public function scopeMy($query, $in = ['draft', 'pending', 'approved', 'archived'])
    {
        $user = Sentinel::getUser();
        return $query->orderBy('created_at', 'desc')->where('user_id', $user->getUserId())->whereIn('status', $in);
    }

    public function scopeNotArchived($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('created_at', 'desc')->where('user_id', $user->getUserId())->where('status', '!=', 'archived');
    }

    /**
     * @param File $file
     * @return ClassifiedsImages
     * @throws \Throwable
     */
    public function attachFile(File $file)
    {
        $image = new ClassifiedsImages();
        $image->classified_id = $this->id;
        $image->file_id = $file->id;
        $image->saveOrFail();

        return $image;
    }

    /**
     * @param bool $save
     * @return bool
     */
    public function prolong($save = true, $force = false)
    {
        if (!($this->can_refresh || (!$this->can_refresh && $force)))
            return false;

        $this->status = 'approved';
        $this->can_refresh = 0;
        if (!$this->expired_at) {
            $this->expired_at = new Carbon();
        }
        $this->expired_at = $this->expired_at->addMonth();

        if (!$save)
            return true;

        return $this->update(['status', 'can_refresh', 'expired_at']);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->description = strip_tags($this->description, config('app.allowable_tags'));

        return parent::save($options);
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $result = parent::delete();
        if ($result) {
            if ($this->thread) {
                $this->thread->thread->delete();
            }
        }

        return $result;
    }
}
