<?php

namespace App\Models\Services;

use App\Models\Traits\ThumbTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Rutorika\Sortable\SortableTrait;

/**
 * Class Service
 * @package App\Models\Services
 */
class Service extends Model
{
    use ThumbTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    use SortableTrait;
    use SoftDeletes;

    const GROUPS = [
        'service' => 'Service',
        'brand' => 'Brand',
    ];

    public $table = 'services';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'parent_id',
        'title',
        'category_id',
        'type',
        'description',
        'data',
        'position'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'image' => 'string',
        'position' => 'int'
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
                'source' => ['category_id', 'parent_id', 'title']
            ]
        ];
    }

    protected static $sortableGroupField = ['category_id', 'parent_id'];

    /**
     * @param string $value
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return (array)json_decode($value);
    }

    /**
     * @param array $value
     */
    public function setDataAttribute($value)
    {
        if (isset($value['key']) && isset($value['value'])) {
            $temp = $value;
            $value = [];
            foreach ($temp['key'] as $i => $key) {
                if ($key) {
                    $value[$key] = $temp['value'][$i] ?? null;
                }
            }
        }
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    /**
     * @return array
     */
    public function getParents()
    {
        $return = [];
        if ($this->parent_id) {
            $return = array_merge([
                $this->parent_id => ['id' => $this->parent->id, 'title' => $this->parent->title, 'entity_type' => 'service']
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

        $sourceImage = '/uploads/services/' . $this->image;
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

            $destinationPath = public_path() . '/uploads/services/';

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
