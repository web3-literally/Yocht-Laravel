<?php

namespace App\Widgets;

use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

class VesselLocation extends AbstractWidget
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
        $this->config['key'] = config('services.google_map.key');

        $currentVessel = Vessel::currentVessel();

        if (is_null($currentVessel)) {
            return '';
        }

        return view('widgets.vessel_location', [
            'config' => $this->config,
            'currentVessel' => $currentVessel,
        ]);
    }
}
