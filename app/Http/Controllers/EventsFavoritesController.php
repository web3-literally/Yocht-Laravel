<?php

namespace App\Http\Controllers;

use App\Models\Events\Event;
use App\Models\Events\FavoriteEvent;
use Sentinel;

/**
 * Class EventsFavoritesController
 * @package App\Http\Controllers
 */
class EventsFavoritesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function my()
    {
        $events = Event::whereIn('id', Sentinel::getUser()->favoriteEvents()->pluck('event_id')->toArray())->paginate(20);

        return view('favorites.events', compact('events'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store($related_member_id, $id)
    {
        $event = Event::find($id);

        if (empty($event)) {
            return abort(404);
        }

        $favorite = FavoriteEvent::my()->where('event_id', $id)->get();

        if ($favorite->isNotEmpty()) {
            return abort(404);
        }

        $item = new FavoriteEvent();
        $item->event_id = $id;
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
        $item = FavoriteEvent::my()->where('event_id', $id);

        if (empty($item)) {
            return abort(404);
        }

        $item->delete();

        return response()->json(true);
    }
}
