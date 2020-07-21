<?php

namespace App\Models\Services;

use App\Models\Traits\ThumbTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use File;

/**
 * Class ServiceCategory
 * @package App\Models\Services
 */
class ServiceCategory extends Model
{
    use ThumbTrait;
    use SoftDeletes;

    public $table = 'services_categories';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'parent_id',
        'label',
        'position'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
        'label' => 'string',
        'image' => 'string',
        'position' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        $instance = $this->hasMany(Service::class, 'category_id', 'id');
        $instance->getQuery()->sorted();
        return $instance;
    }

    /**
     * @return array
     */
    public function getParents()
    {
        $return = [];
        if ($this->parent_id) {
            $return = array_merge([
                $this->parent_id => ['id' => $this->parent->id, 'title' => $this->parent->label, 'entity_type' => 'category']
            ], $this->parent->getParents());
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getParentIds()
    {
        $return = [];
        if ($this->parent_id) {
            $return = array_merge([$this->parent_id], $this->parent->getParentIds());
        }

        return $return;
    }

    /**
     * @return array|string
     */
    public function getParentLabels()
    {
        $return = [];
        if ($this->parent_id) {
            $return = array_merge([$this->parent->label], $this->parent->getParentLabels());
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function hasServices()
    {
        return (bool)$this->services->count();
    }

    /**
     * @return array|string
     */
    public function getFullLabelAttribute()
    {
        return implode(' - ', array_merge([$this->label], $this->getParentLabels()));
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

        $sourceImage = '/uploads/services-category/' . $this->image;
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

            $destinationPath = public_path() . '/uploads/services-category/';

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
