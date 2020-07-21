<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Member\Viewed' => [
            'App\Listeners\Member\Viewed',
        ],
        'App\Events\Member\Subscription\Created' => [
            'App\Listeners\Member\Subscription\Created',
        ],
        'App\Events\Member\Subscription\Canceled' => [
            'App\Listeners\Member\Subscription\Canceled',
        ],
        'App\Events\Job\Published' => [
            'App\Listeners\Job\Published',
        ],
        'App\Events\Job\Changed' => [
            'App\Listeners\Job\Changed',
        ],
        'App\Events\Vessel\Relocate' => [
            'App\Listeners\Vessel\LocationChanged',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
