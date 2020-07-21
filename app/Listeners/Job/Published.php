<?php

namespace App\Listeners\Job;

use App\Events\Job\Published as PublishedEvent;
use App\Mail\Jobs\PrivateInvitation;
use Mail;

class Published
{
    /**
     * Handle the event.
     *
     * @param PublishedEvent $event
     * @return void
     */
    public function handle(PublishedEvent $event)
    {
        if ($event->job->visibility == 'private') {
            foreach ($event->job->load('members')->members as $member) {
                Mail::send(new PrivateInvitation($event->job, $member->user));
            }
        }
    }
}