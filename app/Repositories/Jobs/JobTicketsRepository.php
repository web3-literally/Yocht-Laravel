<?php

namespace App\Repositories\Jobs;

use App\Models\Jobs\JobTickets;
use InfyOm\Generator\Common\BaseRepository;
use Sentinel;

class JobTicketsRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return JobTickets::class;
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
     * @return int|null
     */
    public function pendingTicketsForMe() {
        $relatedId = request('related_member_id');
        if (empty($relatedId) || $relatedId == '-') {
            return null;
        }

        return JobTickets::toMe($relatedId)
            ->published()
            ->get()
            ->count();
    }

    /**
     * @return int
     */
    public function unreadTicketsForMe()
    {
        $relatedId = request('related_member_id');
        if (empty($relatedId) || $relatedId == '-') {
            return null;
        }

        // TODO: Optimize, mysql view can improve performance
        $user_id = Sentinel::getUser()->getUserId();
        $unread = 0;

        JobTickets::forMe($relatedId)->active()->get()->each(function ($item, $key) use ($user_id, &$unread) {
            if ($item->application->thread->thread->isUnread($user_id)) {
                $unread++;
            }
        });

        return $unread;
    }
}
