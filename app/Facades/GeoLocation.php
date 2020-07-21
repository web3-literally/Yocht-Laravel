<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;
use GeoNames\Client as GeoNamesClient;
use LaravelLocalization;

/**
 * Class GeoLocation
 * @package App\Facades
 *
 * TODO: Implement caching
 */
class GeoLocation extends BaseFacade
{
    const SHORT = 'SHORT';
    const MEDIUM = 'MEDIUM';
    const LONG = 'LONG';
    const FULL = 'FULL';

    const SEARCH_STYLE = self::MEDIUM;
    const SEARCH_MAX_ROWS = 30;

    private static $instance = null;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'geolocation';
    }

    /**
     * @return bool
     */
    public static function isDetected()
    {
        return request()->cookie('current_location_city') || request()->cookie('current_location_country');
    }

    /**
     * @return string
     */
    public static function getCurrentLocation()
    {
        return implode(',', [request()->cookie('current_location_city'), request()->cookie('current_location_country')]);
    }

    /**
     * @return GeoNamesClient
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new GeoNamesClient(config('services.geonames.key'));
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    /**
     * @param string $q
     * @param string $country country code ISO-3166
     * @return array|null
     */
    public static function searchCity($q, $country = null)
    {
        if (empty($q))
            return null;

        $results = GeoLocation::getInstance()->search([
            'q' => $q,
            'fuzzy' => '0.8',
            'featureClass' => 'P',
            'country' => $country,
            'style' => self::SEARCH_STYLE,
            'maxRows' => self::SEARCH_MAX_ROWS,
            'lang' => LaravelLocalization::getCurrentLocale()
        ]);

        return $results;
    }

    /**
     * @param string $q
     * @param string $country country code ISO-3166
     * @return array|null
     */
    public static function searchCounty($q, $country = null)
    {
        if (empty($q))
            return null;

        $results = GeoLocation::getInstance()->search([
            'q' => $q,
            'fuzzy' => '0.8',
            'featureCode' => 'ADM2',
            'country' => $country,
            'style' => self::SEARCH_STYLE,
            'maxRows' => self::SEARCH_MAX_ROWS,
            'lang' => LaravelLocalization::getCurrentLocale()
        ]);

        return $results;
    }

    /**
     * @param string $q
     * @param string $country country code ISO-3166
     * @return array|null
     */
    public static function searchState($q, $country = null)
    {
        if (empty($q))
            return null;

        $results = GeoLocation::getInstance()->search([
            'q' => $q,
            'fuzzy' => '0.8',
            'featureCode' => 'ADM1',
            'country' => $country,
            'style' => self::SEARCH_STYLE,
            'maxRows' => self::SEARCH_MAX_ROWS,
            'lang' => LaravelLocalization::getCurrentLocale()
        ]);

        return $results;
    }

    /**
     * @param string $q
     * @return array|null
     */
    public static function searchCountry($q)
    {
        if (empty($q))
            return null;

        $results = GeoLocation::getInstance()->search([
            'q' => $q,
            'fuzzy' => '0.8',
            'featureCode' => 'PCLI',
            'style' => self::SEARCH_STYLE,
            'maxRows' => self::SEARCH_MAX_ROWS,
            'lang' => LaravelLocalization::getCurrentLocale()
        ]);

        return $results;
    }

    /**
     * @param int $id
     * @return \stdClass|null
     */
    public static function get($id)
    {
        if (empty($id))
            return null;

        $result = GeoLocation::getInstance()->get([
            'geonameId' => $id,
            'style' => 'MEDIUM',
            'lang' => LaravelLocalization::getCurrentLocale()
        ]);

        return $result;
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getLabel($id)
    {
        $result = self::get($id);
        if (empty($result))
            return null;

        return $result->countryCode == 'US' ? "{$result->name}, {$result->adminCode1}, {$result->countryName}" : "{$result->name}, {$result->countryName}";
    }

    /**
     * @param int $id
     * @return array|null
     */
    public static function getHierarchy($id)
    {
        if (empty($id))
            return null;

        $result = GeoLocation::getInstance()->hierarchy([
            'geonameId' => $id
        ]);
        if (empty($result))
            return null;

        $ids = [];
        foreach ($result as $item) {
            if (in_array($item->fcode, ['PCLI', 'ADM1', 'ADM2', 'PPL'])) {
                $ids[] = $item->geonameId;
            }
        }

        return $ids;
    }
}
