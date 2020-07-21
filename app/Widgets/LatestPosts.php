<?php

namespace App\Widgets;

use App\Models\News;
use Arrilot\Widgets\AbstractWidget;

class LatestPosts extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    public function run()
    {
        $posts = News::orderBy('date', 'desc')->orderBy('id', 'desc')->limit(2)->get();

        return view('widgets.latest_posts', [
            'posts' => $posts,
            'config' => $this->config,
        ]);
    }
}
