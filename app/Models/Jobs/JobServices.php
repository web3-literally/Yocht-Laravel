<?php

namespace App\Models\Jobs;

use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JobServices
 * @package App\Models\Jobs
 */
class JobServices extends Model
{
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'job_services';

    /**
     * @var array
     */
    public $fillable = [
        'job_id',
        'service_id'
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
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}