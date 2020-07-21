<?php

namespace App\Models\Jobs;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Sentinel;

/**
 * Class JobTickets
 * @package App\Models\Jobs
 */
class JobTickets extends Model
{
    /**
     * @var string
     */
    public $table = 'job_tickets';

    /**
     * @var array
     */
    public $fillable = [
        'applicant_id'
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
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return int
     */
    public function unreadCount()
    {
        $i = 0;
        foreach ($this->applications as $item) {
            if ($item->thread->thread->isUnread(Sentinel::getUser()->getUserId())) {
                $i++;
            }
        }

        return $i;
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id')->withTrashed();
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function application()
    {
        return $this->hasOne(JobApplications::class, 'ticket_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        return $this->hasMany(JobApplications::class, 'ticket_id');
    }

    /**
     * @param Builder $query
     * @param int|null $related_id
     * @param null $statuses
     * @return mixed
     */
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
            ->whereNull($jobsTable . '.deleted_at')
            ->whereIn($jobsTable . '.status', $statuses)
            ->groupBy($this->getTable() . '.id')
            ->select($this->getTable() . '.*');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeForMe($query, $related_id = null)
    {
        $ticketsTable = $this->getTable();
        $jobsTable = (new Job)->getTable();
        $applicationsTable = (new JobApplications())->getTable();

        return $query
            ->join($jobsTable, $ticketsTable . '.job_id', '=', $jobsTable . '.id')
            ->join($applicationsTable, $applicationsTable . '.ticket_id', '=', $ticketsTable . '.id')
            ->orderBy($ticketsTable . '.created_at', 'asc')
            ->where($applicationsTable . '.user_id', (int)$related_id)
            ->whereNull($jobsTable . '.deleted_at')
            ->groupBy($ticketsTable . '.id')
            ->select($ticketsTable . '.*');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeToMe($query, $related_id = null)
    {
        $ticketsTable = $this->getTable();
        $jobsTable = (new Job)->getTable();
        $jobsMembersTable = (new JobMembers())->getTable();

        return $query
            ->join($jobsTable, $ticketsTable . '.job_id', '=', $jobsTable . '.id')
            ->join($jobsMembersTable, $jobsMembersTable . '.job_id', '=', $jobsTable . '.id')
            ->orderBy($ticketsTable . '.created_at', 'asc')
            ->where($jobsMembersTable . '.member_id', (int)$related_id)
            ->whereNull($jobsTable . '.deleted_at')
            ->groupBy($ticketsTable . '.id')
            ->select($ticketsTable . '.*');
    }

    /**
     * @param Builder $query
     * @param $to
     * @param int|null $related_id
     * @return mixed
     */
    public function scopeRelated($query, $to, $related_id = null)
    {
        $ticketsTable = $this->getTable();
        $jobsTable = (new Job)->getTable();

        return $query
            ->join($jobsTable, $ticketsTable . '.job_id', '=', $jobsTable . '.id')
            ->orderBy($ticketsTable . '.created_at', 'desc')
            ->whereNull($jobsTable . '.deleted_at')
            ->where($jobsTable . '.user_id', $to)
            ->where($ticketsTable . '.applicant_id', (int)$related_id)
            ->groupBy($ticketsTable . '.id')
            ->select($ticketsTable . '.*');
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        $jobsTable = (new Job)->getTable();

        return $query
            ->whereIn($jobsTable . '.status', [Job::STATUS_PUBLISHED, Job::STATUS_IN_PROCESS]);
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        $jobsTable = (new Job)->getTable();

        return $query
            ->where($jobsTable . '.status', Job::STATUS_PUBLISHED);
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeInProcess($query)
    {
        $jobsTable = (new Job)->getTable();

        return $query
            ->whereIn($jobsTable . '.status', [Job::STATUS_IN_PROCESS]);
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        $jobsTable = (new Job)->getTable();

        return $query
            ->whereIn($jobsTable . '.status', [Job::STATUS_COMPLETED]);
    }
}
