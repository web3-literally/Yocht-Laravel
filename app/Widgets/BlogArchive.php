<?php

namespace App\Widgets;

use App\Blog;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

/**
 * Class BlogArchive
 * @package App\Widgets
 */
class BlogArchive extends AbstractWidget
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
        $archive = Cache::remember('BlogArchive', 10, function () {
            return Blog::published()
                ->groupBy([DB::raw('YEAR(publish_on)'), DB::raw('MONTH(publish_on)')])
                ->select([DB::raw('YEAR(publish_on) as year'), DB::raw('MONTH(publish_on) as month')])
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->where('publish_on', '<', date('Y-m-01 00:00:00'))
                ->limit(8)
                ->get();
        });

        return view('widgets.blog_archive', [
            'config' => $this->config,
            'archive' => $archive,
        ]);
    }
}
