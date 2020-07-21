<?php

namespace App\Widgets;

use App\Facades\GeoLocation;
use App\Facades\WorldWeatherOnline;
use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class Weather
 * @package App\Widgets
 */
class Weather extends AbstractWidget
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
        $temperature = '-';
        $temperatureUnit = '';
        $temperatureUnitClass = '';
        $weatherIcon = null;
        $weatherIconPhrase = '';
        $weatherWindSpeed = '';
        $weatherWindSpeedUnit = '';
        $weatherWindDirection = '';
        $weatherDetails = '';

        $geoPosition = null;

        $currentVessel = Vessel::currentVessel();

        if (isset($this->config['q']) && $this->config['q']) {
            $geoPosition = WorldWeatherOnline::searchLocation($this->config['q']);
        } elseif ($currentVessel && $currentVessel->location) { // TODO: Event/Listener implementation
            $geoPosition = WorldWeatherOnline::searchLocation($currentVessel->location);
        } elseif (GeoLocation::isDetected()) {
            $geoPosition = WorldWeatherOnline::searchLocation(GeoLocation::getCurrentLocation());
        }

        if ($geoPosition) {
            $forecast = WorldWeatherOnline::getForecastToday($geoPosition);
            if ($forecast) {
                $temperature = $forecast->maxtempF;
                $temperatureUnit = 'F';
                if ($temperatureUnit == 'C') {
                    $temperatureUnitClass = 'celsius';
                }
                if ($temperatureUnit == 'F') {
                    $temperatureUnitClass = 'fahrenheit';
                }
                $weatherIcon = current(current($forecast->hourly)->weatherIconUrl)->value;
                $weatherIconPhrase = current(current($forecast->hourly)->weatherDesc)->value;
                $weatherWindSpeed = current($forecast->hourly)->windspeedMiles;
                $weatherWindSpeedUnit = 'Miles';
                $weatherWindDirection = current($forecast->hourly)->winddir16Point;
                $weatherDetails = current($geoPosition->weatherUrl)->value;
                $location = implode(', ', [current($geoPosition->areaName)->value, current($geoPosition->country)->value]);
            }
        } else {
            $temperature = '?';
        }

        return view('widgets.weather', [
            'config' => $this->config,
            'location' => $location,
            'temperature' => $temperature,
            'temperatureUnit' => $temperatureUnit,
            'temperatureUnitClass' => $temperatureUnitClass,
            'weatherIcon' => $weatherIcon,
            'weatherIconPhrase' => $weatherIconPhrase,
            'weatherWindSpeed' => $weatherWindSpeed,
            'weatherWindSpeedUnit' => $weatherWindSpeedUnit,
            'weatherWindDirection' => $weatherWindDirection,
            'weatherDetails' => $weatherDetails,
        ]);
    }
}
