<?php

namespace App\Widgets;

use App\Models\Classifieds\Classifieds;
use App\Models\Classifieds\ClassifiedsCategory;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

class ClassifiedsCategoriesCounts extends AbstractWidget
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
        $this->config['type'] = $this->config['type'] ?? Classifieds::TYPE_BOAT;

        $categories = Cache::remember('ClassifiedsCategoriesAll_' . $this->config['type'], 3, function () {
            $counters = DB::table('classifieds_categories')
                ->leftJoin('classifieds', 'classifieds.category_id', '=', 'classifieds_categories.id')
                ->where('classifieds_categories.type', $this->config['type'])
                ->whereNull('classifieds.deleted_at')
                ->where('classifieds.status', 'approved')
                ->where('classifieds.type', $this->config['type'])
                ->orderBy('classifieds_categories.title')
                ->groupBy('classifieds_categories.id')
                ->select(['classifieds_categories.id', DB::raw('COUNT(classifieds.id) AS classifieds_count')])
                ->pluck('classifieds_count', 'id');

            $categories = DB::table('classifieds_categories')
                ->where('classifieds_categories.type', $this->config['type'])
                ->orderBy('classifieds_categories.title')
                ->select('classifieds_categories.*')
                ->get();

            $categories->map(function($category) use ($counters) {
                $category->classifieds_count = $counters[$category->id] ?? 0;
                return $category;
            });

            return $categories;
        });

        return view('widgets.classifieds_categories_counts', [
            'config' => $this->config,
            'categories' => $categories,
            'type' => $this->config['type']
        ]);
    }
}
