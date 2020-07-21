<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\Models\Events\Event;

/**
 * Class EventsCalendar
 * @package App\Widgets
 */
class EventsCalendar extends AbstractWidget
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
        $events = Event::upcoming()->get();

        return view('widgets.events_calendar', [
            'config' => $this->config,
            'events' => $events
        ]);
    }
}
