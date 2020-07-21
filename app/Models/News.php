<?php

namespace App\Models;

use App\Models\Traits\ThumbTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use File;


class News extends Model
{
    use SoftDeletes;
    use ThumbTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    public $table = 'news';

    protected $dates = ['deleted_at', 'date'];

    public $fillable = [
        'title',
        'permalink',
        'description',
        'date',
        'image',
        'source_id',
        'hash'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'permalink' => 'string',
        'description' => 'string',
        'image' => 'string',
        'hash' => 'string'
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
                'source' => ['title', 'id']
            ]
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(NewsSource::class, 'source_id', 'id');
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return route('news.show', [
           'year' => $this->date->format('Y'),
           'month' => $this->date->format('m'),
           'slug' => $this->slug,
        ]);
    }

    /**
     * @param bool $save
     * @return $this
     * @throws \Throwable
     */
    public function deleteImage($save = true)
    {
        if ($this->hasImage()) {
            $model = $this;

            $destinationPath = public_path() . '/uploads/news/';

            $folders = File::glob($destinationPath . '*', GLOB_ONLYDIR);
            if ($folders) {
                foreach ($folders as $folder) {
                    $filePath = $destinationPath . basename($folder) . '/' . $model->image;
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }
            $filePath = $destinationPath . $model->image;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $model->image = null;
            if ($save) {
                $model->saveOrFail();
            }
        }

        return $this;
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
            return $placeholderUrl;
        }

        $sourceImage = '/uploads/news/' . $this->image;
        $thumbUrl = $this->genThumb($sourceImage, $size);
        if (!$thumbUrl) {
            return $placeholderUrl;
        }
        return $thumbUrl;
    }
}
