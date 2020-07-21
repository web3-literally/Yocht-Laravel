<?php

namespace App\Models\Vessels;

use App\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Rutorika\Sortable\SortableTrait;
use Sentinel;

/**
 * Class VesselsImage
 *
 * @property File file
 */
class VesselsImage extends Model
{
    use SortableTrait;

    public $timestamps = false;

    public $table = 'vessels_images';

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'vessel_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'vessel_id' => 'int',
        'file_id' => 'int',
        'order' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
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

        $vesselsTable = (new Vessel())->getTable();
        $vesselsImagesTable = (new VesselsImage())->getTable();

        return $query->join($vesselsTable, $vesselsTable . '.id', '=', $vesselsImagesTable . '.vessel_id')
            ->whereIn($vesselsImagesTable . '.vessel_id', $user->vessels()->pluck('id')->all())
            ->select($vesselsImagesTable . '.*');
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
