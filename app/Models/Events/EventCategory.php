<?php

namespace App\Models\Events;

use App\Models\Traits\ThumbTrait;
use Illuminate\Database\Eloquent\Model;
use Sentinel;
use File;

/**
 * Class EventCategory
 * @package App\Models\Events
 */
class EventCategory extends Model
{
    use ThumbTrait;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'events_categories';

    /**
     * @var array
     */
    public $fillable = [
        'label'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'label' => 'string',
        'image' => 'string'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
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

        $sourceImage = '/uploads/events-category/' . $this->image;
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

            $destinationPath = public_path() . '/uploads/events-category/';

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