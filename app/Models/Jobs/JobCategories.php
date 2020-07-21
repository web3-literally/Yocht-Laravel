<?php

namespace App\Models\Jobs;

use App\Models\Services\ServiceCategory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JobCategories
 * @package App\Models\Classifieds
 *
 * @property File file
 */
class JobCategories extends Model
{
    public $timestamps = false;

    public $table = 'job_categories';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'job_id' => 'int',
        'category_id' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
