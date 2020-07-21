<?php

namespace App\Models\Classifieds;

use App\Country;
use App\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Igaster\LaravelCities\Geo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use Sentinel;

/**
 * Class ClassifiedsCategory
 * @package App\Models\Classifieds
 */
class ClassifiedsCategory extends Model implements Seoable
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;

    public $table = 'classifieds_categories';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'title',
        'slug',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'slug' => 'string',
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
                'source' => ['title']
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
    public function getMetaAttribute() {
        return $this->getSeoData();
    }
}
