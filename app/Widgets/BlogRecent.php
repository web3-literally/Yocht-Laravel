<?php

namespace App\Widgets;

use App\Blog;
use Arrilot\Widgets\AbstractWidget;
use Cache;

/**
 * Class BlogRecent
 * @package App\Widgets
 */
class BlogRecent extends AbstractWidget
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
        $posts = Cache::remember('BlogRecent', 10, function () {
             return Blog::published()->orderBy('publish_on', 'desc')->limit(3)->get();
        });

        return view('widgets.blog_recent', [
            'config' => $this->config,
            'posts' => $posts,
        ]);
    }
}
