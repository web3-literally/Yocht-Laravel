<?php

namespace App\Http\Controllers;

use App\Models\Services\Service;
use App\Models\Services\ServiceGroup;
use Cache;
use Illuminate\Http\Request;
use App\Models\Services\ServiceCategory;
use DB;

/**
 * Class ServicesController
 * @package App\Http\Controllers
 */
class ServicesController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function categories(Request $request)
    {
        $categories = ServiceCategory::orderBy('label', 'asc')->get();

        return view('services.categories')->with('categories', $categories);
    }

    /**
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($category_id)
    {
        $category = ServiceCategory::find($category_id);

        if (!$category) {
            return abort(404);
        }

        $services = Service::where('category_id', $category->id)->paginate(10);

        return view('services.category', compact('category', 'services'));
    }

    /**
     * @param int $category_id
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function service($category_id, $slug)
    {
        $service = Service::findBySlug($slug);

        if (!$service) {
            return abort(404);
        }

        return view('services.service', compact('service'));
    }
}
