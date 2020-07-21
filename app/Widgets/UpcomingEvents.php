<?php

namespace App\Widgets;

use App\Models\Events\Event;
use Arrilot\Widgets\AbstractWidget;

class UpcomingEvents extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $events = Event::upcoming()->orderBy('starts_at', 'asc')->limit(3)->get();

        return view('widgets.upcoming_events', [
            'events' => $events,
            'config' => $this->config,
        ]);
    }
}
