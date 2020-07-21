<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JobPeriod
 * @package App\Models\Jobs
 */
class JobPeriod extends Model
{
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'jobs_periods';

    /**
     * @var array
     */
    public $fillable = [
        'job_id',
        'period_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = [];

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
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }
}