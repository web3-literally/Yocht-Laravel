<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classifieds\Classifieds;
use App\Models\Classifieds\ClassifiedsManufacturer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ManufacturersController
 * @package App\Http\Controllers
 */
class ManufacturersController extends Controller
{
    /**
     * @param string type
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'type' => 'required|' . Rule::in(array_keys(Classifieds::getTypes())),
            'search' => 'required|min:1'
        ]);

        $results = ClassifiedsManufacturer::where('type', $request->get('type'))->where('title', 'like', '%' . $request->get('search') . '%')->orderBy('title')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->title
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }
}
