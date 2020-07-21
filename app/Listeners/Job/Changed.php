<?php

namespace App\Listeners\Job;

use App\Events\Job\Changed as ChangedEvent;
use App\Mail\Jobs\TicketChanged;
use Mail;

class Changed
{
    /**
     * Handle the event.
     *
     * @param ChangedEvent $event
     * @return void
     */
    public function handle(ChangedEvent $event)
    {
        if ($event->job->status == 'in_process') {
            Mail::send(new TicketChanged($event->job));
        }
    }
}