<?php

namespace App\Listeners\Member\Subscription;

use App\Events\Member\Subscription\Created as SubscriptionCreated;
use App\Mail\Member\Subscribed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class Created
{
    /**
     * Handle the event.
     *
     * @param SubscriptionCreated $event
     * @return void
     */
    public function handle(SubscriptionCreated $event)
    {
        Mail::send(new Subscribed($event->subscription));
    }
}
