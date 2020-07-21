<?php

namespace App\Models\Jobs;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JobMembers
 * @package App\Models\Jobs
 */
class JobMembers extends Model
{
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'jobs_members';

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
    public function user()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}