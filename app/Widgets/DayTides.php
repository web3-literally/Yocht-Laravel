<?php

namespace App\Widgets;

use App\Facades\GeoLocation;
use App\Facades\WorldWeatherOnline;
use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class DayTides
 * @package App\Widgets
 */
class DayTides extends AbstractWidget
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
        $location = '-';

        $geoPosition = null;

        $currentVessel = Vessel::currentVessel();

        if ($currentVessel && $currentVessel->location) { // TODO: Event/Listener implementation
            $geoPosition = WorldWeatherOnline::searchLocation($currentVessel->location);
        } elseif (GeoLocation::isDetected()) {
            $geoPosition = WorldWeatherOnline::searchLocation(GeoLocation::getCurrentLocation());
        }

        $heights = [];

        if ($geoPosition) {
            $forecast = WorldWeatherOnline::getForecast($geoPosition);
            if ($forecast) {
                foreach($forecast as $day) {
                    $tides = current($day->tides)->tide_data;
                    foreach($tides as $tide) {
                        $heights[] = $tide;
                    }
                }
                $location = implode(', ', [current($geoPosition->areaName)->value, current($geoPosition->country)->value]);
            }
        }

        return view('widgets.day_tides', [
            'config' => $this->config,
            'heights' => $heights,
            'location' => $location
        ]);
    }
}
