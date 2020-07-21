<?php

namespace App\Helpers;

/**
 * Class Tasks
 * @package App\Helpers
 */
class Tasks
{
    /**
     * @return string
     */
    public static function getTaskManagerTitle()
    {
        $title = trans('tasks.tasks');

        $related = RelatedProfile::currentRelatedMember();
        if ($related) {
            if ($related->isBusinessAccount()) {
                $title = trans('general.reminder_management');
            }
            if ($related->isVesselAccount()) {
                $title = trans('general.vessel_manager');
            }
        }

        return (string)$title;
    }
}