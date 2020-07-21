<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rutorika\Sortable\SortableTrait;
use Sentinel;

/**
 * Class ProfileAttachment
 * @package App
 */
class ProfileAttachment extends Model
{
    use SortableTrait;

    public $timestamps = false;

    public $table = 'profile_attachments';

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'profile_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'profile_id' => 'int',
        'file_id' => 'int',
        'order' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
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
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        return $this->file->getThumb($size);
    }
}
