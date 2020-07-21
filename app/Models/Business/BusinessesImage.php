<?php

namespace App\Models\Business;

use App\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Rutorika\Sortable\SortableTrait;
use Sentinel;

/**
 * Class BusinessesImage
 * *
 * @property File file
 */
class BusinessesImage extends Model
{
    use SortableTrait;

    public $timestamps = false;

    public $table = 'businesses_images';

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'business_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'business_id' => 'int',
        'file_id' => 'int',
        'order' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        return $this->file->cleanup() && parent::delete() && $this->file->delete(false);
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        $businessesTable = (new Business())->getTable();
        $businessesImagesTable = (new BusinessesImage())->getTable();
        return $query->join($businessesTable, $businessesTable . '.id', '=', $businessesImagesTable . '.business_id')->where($businessesTable . '.user_id', $user->getUserId())->select($businessesImagesTable . '.*');
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        return $this->file->getThumb($size);
    }

    /**
     * @return string
     */
    public function getOriginalImage()
    {
        return $this->file->getOriginalImage();
    }
}
