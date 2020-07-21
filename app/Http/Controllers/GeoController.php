<?php

namespace App\Http\Controllers;

use App\Facades\GeoLocation;
use Illuminate\Http\Request;

/**
 * Class GeoController
 * @package App\Http\Controllers
 */
class GeoController extends Controller
{
    /**
     * @param $q
     * @return \Illuminate\Http\JsonResponse
     */
    public function findCity(Request $request)
    {
        $q = $request->get('q');
        if (empty($q))
            abort(400);

        $results = GeoLocation::searchCity($q);

        return response()->json($results);
    }
}
