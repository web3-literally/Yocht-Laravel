<?php

namespace App\Http\Controllers;

use App\Models\Classifieds\Classifieds;
use App\Models\Classifieds\FavoriteClassified;
use Sentinel;

/**
 * Class ClassifiedsFavoritesController
 * @package App\Http\Controllers
 */
class ClassifiedsFavoritesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function my()
    {
        $classifieds = Classifieds::published()->whereIn('id', Sentinel::getUser()->favoriteClassifieds()->pluck('classified_id')->toArray())->paginate(20);

        return view('favorites.classifieds', compact('classifieds'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store($related_member_id, $id)
    {
        $classified = Classifieds::published()->find($id);

        if (empty($classified)) {
            return abort(404);
        }

        $favorite = FavoriteClassified::my()->where('classified_id', $id)->get();

        if ($favorite->isNotEmpty()) {
            return abort(404);
        }

        $item = new FavoriteClassified();
        $item->classified_id = $id;
        $item->user_id = Sentinel::getUser()->getUserId();

        $item->saveOrFail();

        return response()->json(true);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($related_member_id, $id)
    {
        $item = FavoriteClassified::my()->where('classified_id', $id);

        if (empty($item)) {
            return abort(404);
        }

        $item->delete();

        return response()->json(true);
    }
}
