<?php

namespace App\Widgets;

use App\Blog;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class BlogTags
 * @package App\Widgets
 */
class BlogTags extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $tags = Blog::allTags();

        return view('widgets.blog_tags', [
            'config' => $this->config,
            'tags' => $tags,
        ]);
    }
}
