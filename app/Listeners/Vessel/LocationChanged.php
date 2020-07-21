<?php

namespace App\Listeners\Vessel;

use App\Events\Vessel\Relocate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationChanged
{
    /**
     * Handle the event.
     *
     * @param Relocate $event
     * @return void
     */
    public function handle(Relocate $event)
    {
        //
    }
}