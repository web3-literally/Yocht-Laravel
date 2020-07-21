<?php

namespace App\Models\Classifieds;

use App\Country;
use App\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Hootlex\Moderation\Moderatable;
use Igaster\LaravelCities\Geo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use Sentinel;

/**
 * Class ClassifiedsManufacturer
 * @package App\Models\Classifieds
 */
class ClassifiedsManufacturer extends Model implements Seoable
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;
    use Moderatable;

    public $table = 'classifieds_manufacturers';

    public static $strictModeration = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'title',
        'slug',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'label' => 'string',
        'slug' => 'string',
        'category_id' => 'integer',
        'type' => 'string',
    ];

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
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
                'source' => ['title', 'type']
            ]
        ];
    }

    public function seoable()
    {
        $this->seo()->setTitleRaw($this->title)->setKeywordsRaw('')->setDescriptionRaw('');
    }

    /**
     * @return mixed
     */
    public function getMetaAttribute()
    {
        return $this->getSeoData();
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute() {
        return '';//$this->approved == 1 ? 'Approved' : 'Pending';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categories()
    {
        return $this->hasManyThrough(ClassifiedsCategory::class, ClassifiedsCategoriesManufacturers::class, 'manufacturer_id',  'id', 'id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(ClassifiedsCategoriesManufacturers::class,  'manufacturer_id', 'id');
    }
}
