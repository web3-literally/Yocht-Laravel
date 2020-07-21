<?php

namespace App\Widgets;

use App\Facades\GeoLocation;
use App\Facades\WorldWeatherOnline;
use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class SunMoonTime
 * @package App\Widgets
 */
class SunMoonTime extends AbstractWidget
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

        $sunTimes = [];
        $moonTimes = [];

        if ($geoPosition) {
            $forecast = WorldWeatherOnline::getForecastToday($geoPosition);
            if ($forecast) {
                $astronomy = current($forecast->astronomy);
                $sunTimes['sunrise'] = (new \DateTime($astronomy->sunrise));
                $sunTimes['sunset'] = (new \DateTime($astronomy->sunset));
                if (!($astronomy->moonrise == 'No moonrise')) {
                    $moonTimes['moonrise'] = (new \DateTime($astronomy->moonrise));
                }
                if (!($astronomy->moonset == 'No moonset')) {
                    $moonTimes['moonset'] = (new \DateTime($astronomy->moonset));
                }
                $location = implode(', ', [current($geoPosition->areaName)->value, current($geoPosition->country)->value]);
            }
        }

        return view('widgets.sun_moon_time', [
            'config' => $this->config,
            'location' => $location,
            'sunriseRise' => isset($sunTimes['sunrise']) ? $sunTimes['sunrise']->format('g:i a') : '-',
            'sunriseSet' => isset($sunTimes['sunset']) ? $sunTimes['sunset']->format('g:i a') : '-',
            'moonRise' => isset($moonTimes['moonrise']) ? $moonTimes['moonrise']->format('g:i a') : '-',
            'moonSet' => isset($moonTimes['moonset']) ? $moonTimes['moonset']->format('g:i a') : '-',
        ]);
    }
}
