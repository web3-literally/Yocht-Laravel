<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Sentinel;

class PrimaryVessel extends AbstractWidget
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
        $vessel = Sentinel::getUser()->primaryVessel;
        if (empty($vessel)) {
            return '';
        }

        return view('widgets.primary_vessel', [
            'config' => $this->config,
            'vessel' => $vessel
        ]);
    }
}
