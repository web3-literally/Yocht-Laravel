<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\Helpers\SunCalc\SunCalc;

/**
 * Class SunMoonTimeByGeoPoint
 * @package App\Widgets
 * @deprecated
 */
class SunMoonTimeByGeoPoint extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'lat' => null,
        'lng' => null,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        if (request()->cookie('current_location')) {
            $lat = request()->cookie('current_location_lat');
            $lng = request()->cookie('current_location_lng');
            if (is_null($this->config['lat']) && $lat) {
                $this->config['lat'] = $lat;
            }
            if (is_null($this->config['lng']) && $lng) {
                $this->config['lng'] = $lng;
            }
        }

        $sunTimes = [];
        $moonTimes = [];

        if ($this->config['lat'] &&  $this->config['lng']) {
            $sc = new SunCalc(new \DateTime(), $this->config['lat'], $this->config['lng']);
            $sunTimes = $sc->getSunTimes();
            $moonTimes = $sc->getMoonTimes();
        }

        return view('widgets.sun_moon_time', [
            'config' => $this->config,
            'sunriseRise' => isset($sunTimes['sunrise']) ? $sunTimes['sunrise']->format('g:i a') : '-',
            'sunriseSet' => isset($sunTimes['sunset']) ? $sunTimes['sunset']->format('g:i a') : '-',
            'moonRise' => isset($moonTimes['moonrise']) ? $moonTimes['moonrise']->format('g:i a') : '-',
            'moonSet' => isset($moonTimes['moonset']) ? $moonTimes['moonset']->format('g:i a') : '-',
        ]);
    }
}
