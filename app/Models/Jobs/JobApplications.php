<?php

namespace App\Models\Jobs;

use App\File;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\File as Attachment;
use Sentinel;

/**
 * Class JobApplications
 * @package App\Models\Jobs
 */
class JobApplications extends Model
{
    /**
     * @var string
     */
    public $table = 'job_applications';

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
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(JobTickets::class, 'ticket_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thread()
    {
        return $this->hasOne(JobApplicationsMessengerThreads::class, 'application_id', 'id');
    }

    public function scopeMy($query, $related_id = null, $statuses = null)
    {
        $user = Sentinel::getUser();

        if (empty($statuses)) {
            $statuses = [Job::STATUS_PUBLISHED, Job::STATUS_IN_PROCESS, Job::STATUS_COMPLETED];
        }

        $jobsTable = Job::getModel()->getTable();
        return $query->join($jobsTable, 'job_id', '=', $jobsTable . '.id')
            ->where(function ($query) use ($jobsTable, $user, $related_id) {
                $query->where($jobsTable . '.user_id', $user->getUserId());
                if (is_null($related_id)) {
                    $query->whereNull($jobsTable . '.vessel_id');
                } else {
                    $query->where($jobsTable . '.related_member_id', $related_id);
                }
            })
            ->orderBy($this->getTable() . '.job_id', 'asc')
            ->orderBy($this->getTable() . '.created_at', 'asc')
            ->where($jobsTable . '.user_id', $user->getUserId())
            ->whereIn($jobsTable . '.status', $statuses)
            ->groupBy($this->getTable() . '.id')
            ->select($this->getTable() . '.*');
    }

    public function scopeForJob($query, $id)
    {
        return $query->where($this->getTable() . '.job_id', $id);
    }

    public function scopeForTicket($query, $id)
    {
        return $query->where($this->getTable() . '.ticket_id', $id);
    }

    /**
     * Mark the application as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments($owner_id = null)
    {
        $relation = $this->hasMany(JobApplicationAttachments::class, 'application_id', 'id');
        if (!is_null($owner_id)) {
            $relation->where(JobApplicationAttachments::getModel()->getTable() . '.owner_id', $owner_id);
        }

        return $relation;
    }

    /**
     * @param File $file
     * @return JobApplicationAttachments
     * @throws \Throwable
     */
    public function attachFile(File $file)
    {
        $link = new JobApplicationAttachments();
        $link->application_id = $this->id;
        $link->file_id = $file->id;
        $link->owner_id = Sentinel::getUser()->getUserId();
        $link->saveOrFail();

        return $link;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (is_null($this->ticket_id)) {
            $this->job->ticket->id;
        }

        return parent::save($options);
    }
}