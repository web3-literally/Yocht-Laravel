<?php

namespace App\Helpers;

use Curl;
use Cache;

class Geocoding
{
    /**
     * @param string $address
     * @return mixed
     */
	public static function latlngLookup(string $address) {
	    $key = 'SearchLocation_' . md5($address);

        $response = Cache::remember($key, 365 * 1440, function () use ($address) {
            return Curl::to('https://maps.googleapis.com/maps/api/geocode/json')
                ->withData([
                    'address' => $address,
                    'key' => config('services.geocoding.key')
                ])
                ->asJson()
                ->get();
        });

        if (empty($response) || ($response && $response->status !== 'OK')) {
            Cache::forget($key);
        }

        return $response;
    }
}
