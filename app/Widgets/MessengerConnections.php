<?php

namespace App\Widgets;

use App\Models\Messenger\Thread;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class MessengerConnections
 * @package App\Widgets
 */
class MessengerConnections extends AbstractWidget
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
        $connections = Thread::my()->latest('latest_order')->take(2)->get();

        return view('widgets.messenger_connections', [
            'config' => $this->config,
            'connections' => $connections,
        ]);
    }
}
