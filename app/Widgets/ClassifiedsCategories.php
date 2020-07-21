<?php

namespace App\Widgets;

use App\Models\Classifieds\Classifieds;
use App\Models\Classifieds\ClassifiedsCategory;
use Arrilot\Widgets\AbstractWidget;
use DB;
use Cache;

/**
 * Class ClassifiedsCategories
 * @package App\Widgets
 */
class ClassifiedsCategories extends AbstractWidget
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

        $categories = Cache::remember('ClassifiedsCategories_' . $this->config['type'], 3, function () {
            $classifiedsTable = (new Classifieds())->getTable();
            $categoriesTable = (new ClassifiedsCategory())->getTable();
            return ClassifiedsCategory::join($classifiedsTable, $categoriesTable . '.id', '=', $classifiedsTable . '.category_id')
                ->where($categoriesTable . '.type', $this->config['type'])
                ->whereNull($classifiedsTable . '.deleted_at')
                ->where($classifiedsTable . '.status', 'approved')
                ->where($classifiedsTable . '.type', $this->config['type'])
                ->orderBy('classifieds_count', 'desc')
                ->groupBy($categoriesTable . '.id')
                ->select([$categoriesTable . '.*', DB::raw('COUNT(' . $classifiedsTable . '.id' . ') AS classifieds_count')])
                ->limit(6)
                ->get();
        });

        return view('widgets.classifieds_categories', [
            'config' => $this->config,
            'categories' => $categories,
            'type' => $this->config['type']
        ]);
    }
}
