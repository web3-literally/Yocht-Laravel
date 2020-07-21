<?php

namespace App\Listeners\Member\Subscription;

use App\Events\Member\Subscription\Canceled as SubscriptionCanceled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class Canceled
{
    /**
     * Handle the event.
     *
     * @param SubscriptionCanceled $event
     * @return void
     */
    public function handle(SubscriptionCanceled $event)
    {
        Mail::send(new \App\Mail\Member\Canceled($event->subscription));
    }
}
