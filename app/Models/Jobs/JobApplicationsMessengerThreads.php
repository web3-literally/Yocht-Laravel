<?php

namespace App\Models\Jobs;

use App\Models\Messenger\Thread;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JobApplicationsMessengerThreads
 * @package App\Models\Jobs
 */
class JobApplicationsMessengerThreads extends Model
{
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'job_applications_messenger_threads';

    /**
     * @var array
     */
    public $fillable = [];

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
    public function application()
    {
        return $this->belongsTo(JobApplications::class, 'application_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }
}