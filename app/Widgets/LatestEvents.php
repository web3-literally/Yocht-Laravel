<?php

namespace App\Widgets;

use App\Models\Events\Event;
use Arrilot\Widgets\AbstractWidget;

class LatestEvents extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $events = Event::upcoming()->orderBy('starts_at', 'asc')->limit(2)->get();

        return view('widgets.latest_events', [
            'events' => $events,
            'config' => $this->config,
        ]);
    }
}
