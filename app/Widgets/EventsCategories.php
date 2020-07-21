<?php

namespace App\Widgets;

use App\Models\Events\Event;
use App\Models\Events\EventCategory;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

/**
 * Class EventsCategories
 * @package App\Widgets
 */
class EventsCategories extends AbstractWidget
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
        $categories = Cache::remember('EventCategories', 10, function () {
            $categoriesTable = (new EventCategory())->getTable();
            $eventsTable = (new Event())->getTable();
            return EventCategory::leftJoin($eventsTable, $categoriesTable . '.id', '=', $eventsTable . '.category_id')
                ->where($eventsTable . '.starts_at', '>=', date('Y-m-d 00:00:00'))
                ->orderBy('events_count', 'desc')
                ->groupBy($categoriesTable . '.id')
                ->select([$categoriesTable . '.*', DB::raw('COUNT(' . $eventsTable . '.id) AS events_count')])
                ->having('events_count', '>', 0)
                ->get();
        });

        return view('widgets.events_categories', [
            'config' => $this->config,
            'categories' => $categories,
        ]);
    }
}
