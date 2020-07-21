<?php

namespace App;

use App\Models\Traits\ThumbTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use File;

class BlogCategory extends Model {
    use Sluggable;
    use SluggableScopeHelpers;
    use ThumbTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'blog_categories';

    protected $guarded = ['id'];

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

    public function blog()
    {
        return $this->hasMany(Blog::class);
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

        $sourceImage = '/uploads/blog-category/' . $this->image;
        $thumbUrl = $this->genThumb($sourceImage, $size);
        if (!$thumbUrl) {
            return $placeholderUrl;
        }
        return $thumbUrl;
    }

    /**
     * @param bool $save
     * @return $this
     * @throws \Throwable
     */
    public function deleteImage($save = true)
    {
        if ($this->hasImage()) {
            $category = $this;

            $destinationPath = public_path() . '/uploads/blog-category/';

            $folders = File::glob($destinationPath . '*', GLOB_ONLYDIR);
            if ($folders) {
                foreach ($folders as $folder) {
                    $filePath = $destinationPath . basename($folder) . '/' . $category->image;
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }
            $filePath = $destinationPath . $category->image;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $category->image = null;
            if ($save) {
                $category->saveOrFail();
            }
        }

        return $this;
    }
}
