<?php

namespace App\Listeners\Member;

use App\Events\Member\Viewed as MemberViewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sentinel;

class Viewed
{
    /**
     * Handle the event.
     *
     * @param MemberViewed $event
     * @return void
     */
    public function handle(MemberViewed $event)
    {
        if (Sentinel::check() && $event->member->id != Sentinel::getUser()->getUserId()) {
            $by = Sentinel::getUser();
            activity('Member Visited')
                ->performedOn($event->member)
                ->causedBy($by)
                ->withProperty('referer', request()->headers->get('referer'))
                ->log("{$event->member->member_title} page visited by {$by->member_title}");
        }
    }
}