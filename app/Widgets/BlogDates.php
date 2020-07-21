<?php

namespace App\Widgets;

use App\Blog;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

/**
 * Class BlogDates
 * @package App\Widgets
 */
class BlogDates extends AbstractWidget
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
        $dates = Cache::remember('BlogDates', 10, function () {
            return Blog::published()
                ->orderBy('publish_on', 'desc')
                ->groupBy(DB::raw('CAST(publish_on AS DATE)'))
                ->select(['publish_on', DB::raw('COUNT(id) AS posts_count')])
                ->limit(5)
                ->get();
        });

        return view('widgets.blog_dates', [
            'config' => $this->config,
            'dates' => $dates,
        ]);
    }
}
