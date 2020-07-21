<?php

namespace App;

use App\Models\Traits\FullTextSearchTrait;
use App\Models\Traits\ThumbTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Cviebrock\EloquentTaggable\Taggable;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use App\File as Attachment;

class Blog extends Model implements Seoable {
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use Taggable;
    use ThumbTrait;
    use SeoableTrait;
    use FullTextSearchTrait;
    use ElasticquentTrait;

    const STATUS_PUBLISHED = 'published';
    const STATUS_DRAFT = 'draft';
    const STATUSES = [self::STATUS_DRAFT, self::STATUS_PUBLISHED];

    protected $casts = [
        'publish_on' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'content'
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
                'source' => 'title'
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
        return 'blog_posts';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'slug' => [
            'type' => 'text'
        ],
        'title' => [
            'type' => 'text'
        ],
        'content' => [
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
    );

    /**
     * @return mixed
     */
    public function getMetaAttribute() {
        return $this->getSeoData();
    }

    protected $table = 'blogs';

    protected $guarded = ['id'];

    protected function closeHtmlTags($html) {
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (in_array($openedtags[$i], ['img', 'br', 'hr', 'input', 'keygen', 'area', 'col', 'command', 'embed', 'param', 'source', 'track', 'wbr']))
                continue;
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    /**
     * @return string
     */
    public function shortContent()
    {
        $parts = preg_split('/<!--more-->/i', $this->content);
        if (count($parts) > 1) {
            return $this->closeHtmlTags($parts[0] . ' ...');
        }
        return $parts[0];
    }

    /**
     * @return string
     */
    public function fullContent()
    {
        return $this->content;
    }

    public function publishOnShort()
    {
        return $this->publish_on->toFormattedDateString();
    }

    public function publishOnFull()
    {
        return $this->publish_on->toDayDateTimeString();
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }
    public function category()
    {
        return $this->belongsTo(BlogCategory::class,'blog_category_id');
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getBlogcategoryAttribute()
    {
        return $this->category->pluck('id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', '=', self::STATUS_PUBLISHED)->where('publish_on', '<', date('Y-m-d H:i:s'));
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return (bool)$this->image;
    }

    /**
     * @param $size
     * @return bool|string
     */
    public function getThumb($size)
    {
        $placeholderUrl = str_replace('{size}', $size, config('app.placeholder_url'));
        if (!$this->hasImage()) {
            //$placeholderUrl = preg_replace('/&text=([^&]*)$/', '', $placeholderUrl);
            return $placeholderUrl;
        }

        $sourceImage = '/uploads/blog/' . $this->image;
        $thumbUrl = $this->genThumb($sourceImage, $size);
        if (!$thumbUrl) {
            return $placeholderUrl;
        }
        return $thumbUrl;
    }

    public function hasVideo()
    {
        return !is_null($this->video_id);
    }

    public function video()
    {
        return $this->hasOne(Attachment::class, 'id', 'video_id');
    }
}
