<?php

namespace App\Models\Classifieds;

use App\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Rutorika\Sortable\SortableTrait;
use Sentinel;

/**
 * Class ClassifiedsImages
 * @package App\Models\Classifieds
 */
class ClassifiedsImages extends Model
{
    use SortableTrait;

    public $timestamps = false;

    public $table = 'classifieds_images';

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'classified_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'classified_id' => 'int',
        'file_id' => 'int',
        'order' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified()
    {
        return $this->belongsTo(Сlassifieds::class, 'classified_id');
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
        $classifiedsTable = (new Classifieds())->getTable();
        $classifiedsImagesTable = (new ClassifiedsImages())->getTable();
        return $query->join($classifiedsTable, $classifiedsTable . '.id', '=', $classifiedsImagesTable . '.classified_id')->where($classifiedsTable . '.user_id', $user->getUserId())->select($classifiedsImagesTable . '.*');
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        return $this->file->getThumb($size);
    }
}
