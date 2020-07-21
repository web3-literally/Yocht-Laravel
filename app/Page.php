<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use MadWeb\Seoable\Traits\SeoableTrait;
use MadWeb\Seoable\Contracts\Seoable;

/**
 * Class Page
 * @package App
 */
class Page extends Model implements Seoable
{
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
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

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getCSSClasses() {
        return $this->css_class ? implode(' ', explode(',', $this->css_class)) : '';
    }
}
