<?php

namespace App\Repositories;

use App\Helpers\Owner as OwnerHelper;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobTickets;
use App\User;
use Carbon\Carbon;
use Sentinel;
use InfyOm\Generator\Common\BaseRepository;

class TicketsRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Job::class;
    }

    /**
     * Name of page parameter
     * @var string
     */
    protected $pageName = 'page';

    /**
     * @param string $name
     * @return $this
     */
    public function setPageName(string $name = 'page')
    {
        $this->pageName = $name;

        return $this;
    }

    /**
     * @param User $related
     * @param Carbon $start
     * @param Carbon|null $end
     * @param callable $callback
     * @return \Illuminate\Support\Collection
     */
    public function getTicketsByDate($related, Carbon $start, Carbon $end = null, callable $callback = null)
    {
        if (is_null($end)) {
            $end = $start->copy();
        }

        $start = $start->startOfDay();
        $end = $end->endOfDay();

        $ticketTable = JobTickets::getModel()->getTable();
        $jobTable = Job::getModel()->getTable();

        $tickets = collect([]);

        if ($related->isBusinessAccount()) {
            $builder = JobTickets::join($jobTable, $ticketTable . '.job_id', '=', $jobTable . '.id')
                ->where(function ($query) use ($jobTable, $related) {
                    $query->where(function ($query) use ($jobTable, $related) {
                        $query->where($jobTable . '.applicant_id', $related->id)->where($jobTable . '.status', Job::STATUS_IN_PROCESS);
                    })->orWhere($jobTable . '.related_member_id', $related->id);
                })
                ->whereBetween('starts_at', [$start, $end])
                ->select($ticketTable . '.*');

            if ($callback) {
                call_user_func($callback, $builder);
            }

            $tickets = $builder->get();
        }

        if ($related->isVesselAccount()) {
            /** @var User $user */
            $user = Sentinel::getUser();

            if ($user->isMemberOwnerAccount() || $user->isCaptainAccount()) {
                $query = JobTickets::join($jobTable, $ticketTable . '.job_id', '=', $jobTable . '.id');
                $query->where($jobTable . '.related_member_id', $related->id);
                $query
                    ->where('user_id', OwnerHelper::currentOwner()->id)
                    ->where($jobTable . '.status', Job::STATUS_IN_PROCESS)
                    ->whereBetween('starts_at', [$start, $end])
                    ->select($ticketTable . '.*');
                $tickets = $query->get();
            } else {
                $tickets = JobTickets::join($jobTable, $ticketTable . '.job_id', '=', $jobTable . '.id')
                    ->where($jobTable . '.related_member_id', $related->id)
                    ->where($jobTable . '.applicant_id', $user->id)
                    ->where($jobTable . '.status', Job::STATUS_IN_PROCESS)
                    ->whereBetween('starts_at', [$start, $end])
                    ->select($ticketTable . '.*')
                    ->get();
            }
        }

        return $tickets;
    }
}
