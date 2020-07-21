<?php

namespace App\Repositories;

use App\Models\Tasks\Task;
use InfyOm\Generator\Common\BaseRepository;

class TaskRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Task::class;
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
    public function activeTasksCount()
    {
        if ($relatedMember = \App\Helpers\RelatedProfile::currentRelatedMember()) {
            return Task::assignedToMe($relatedMember->id)
                ->whereIn(Task::getModel()->getTable() . '.status', ['', 'snoozed', 'acknowledge'])
                ->get()->count();
        }
    }
}
