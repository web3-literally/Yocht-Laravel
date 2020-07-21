<?php

namespace App\Widgets;

use App\Blog;
use App\BlogCategory;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

/**
 * Class BlogCategories
 * @package App\Widgets
 */
class BlogCategories extends AbstractWidget
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
        $categories = Cache::remember('BlogCategories', 10, function () {
            $categoriesTable = (new BlogCategory())->getTable();
            $postsTable = (new Blog())->getTable();
            return BlogCategory::join($postsTable, $categoriesTable . '.id', '=', $postsTable . '.blog_category_id')
                ->orderBy('posts_count', 'desc')
                ->groupBy($categoriesTable . '.id')
                ->select([$categoriesTable . '.*', DB::raw('COUNT(' . $postsTable . '.id) AS posts_count')])
                ->having('posts_count', '>', 0)
                ->get();
        });

        return view('widgets.blog_categories', [
            'config' => $this->config,
            'categories' => $categories,
        ]);
    }
}
