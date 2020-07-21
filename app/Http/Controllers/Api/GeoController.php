<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Igaster\LaravelCities\Geo;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

/**
 * Class ServicesController
 * @package App\Http\Controllers
 */
class GeoController extends Controller
{
    /**
     * @param MessageBag $messageBag
     */
    public function __construct(MessageBag $messageBag)
    {
        parent::__construct($messageBag);
    }

    /**
     * @param Request $resuest
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCity(Request $resuest)
    {
        if (empty($resuest->get('q'))) {
            return response()->json([]);
        }
        $results = Geo::search($resuest->get('q'))->where('level', 'ADM3')->orderBy('name', 'ASC')->get();
        return response()->json($results);
    }

    /**
     * @param Request $resuest
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchLocation(Request $resuest)
    {
        if (empty($resuest->get('q'))) {
            return response()->json([]);
        }
        $results = Geo::search($resuest->get('q'))->where('level', '!=', 'PPLC')->orderByRaw('FIELD(level, "PCLI", "ADM1", "ADM2", "ADM3")')->orderBy('name', 'asc')->get();
        $respond = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'level' => $item->level,
                'lng' => $item->long,
                'lat' => $item->lat,
                'belongs' => $item->getAncensors()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'level' => $item->level,
                        'lng' => $item->long,
                        'lat' => $item->lat
                    ];
                })
            ];
        });
        return response()->json($respond);
    }
}