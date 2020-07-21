<?php

namespace App\Facades;

use Curl;
use Cache;
use Storage;
use Illuminate\Support\Facades\Facade as BaseFacade;

class WorldWeatherOnline extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'worldweatheronline';
    }

    /**
     * @return null|string
     */
    protected static function getKey()
    {
        return config('services.worldweatheronline.key');
    }

    /**
     * @param string $q
     * @return \stdClass|null
     */
    public static function searchLocation($q)
    {
        return current(self::searchLocations($q));
    }

    /**
     * @param string $q
     * @return \stdClass[]
     */
    public static function searchLocations($q)
    {
        $response = Cache::remember('WorldWeatherOnline_Locations_' . md5($q), 365 * 1440, function () use ($q) {
            $response = Curl::to('https://api.worldweatheronline.com/premium/v1/search.ashx')
                //->enableDebug(Storage::disk('')->path('/').'/logFile.txt')
                ->withData([
                    'query' => $q,
                    'format' => 'json',
                    'key' => self::getKey()
                ])
                ->asJson()
                ->get();

            return $response->search_api->result ?? [];
        });

        return $response;
    }

    /**
     * @param mixed $location
     * @param int $tp forecast time interval
     * @return \stdClass[]
     */
    public static function getForecast($location, $tp = 24)
    {
        if ($location instanceof \stdClass) {
            $location = "{$location->latitude},{$location->longitude}";
        }

        $response = Cache::remember('WorldWeatherOnline_Forecast_' . md5("{$location},{$tp}"), 15, function () use ($location, $tp) {
            $response = Curl::to('https://api.worldweatheronline.com/premium/v1/marine.ashx')
                //->enableDebug(Storage::disk('')->path('/').'/logFile.txt')
                ->withData([
                    'q' => $location,
                    'tp' => $tp,
                    'fx' => 'yes',
                    'tide' => 'yes',
                    'format' => 'json',
                    'key' => self::getKey()
                ])
                ->asJson()
                ->get();
            return $response->data->weather ?? [];
        });

        return $response;
    }

    /**
     * @param mixed $location
     * @return \stdClass
     */
    public static function getForecastToday($location)
    {
        return current(self::getForecast($location, 24));
    }
}
