<?php

namespace App\Widgets;

use App\Models\Jobs\Job;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

/**
 * Class ServicesCategories
 * @package App\Widgets
 */
class ServicesCategories extends AbstractWidget
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
        $categories = Cache::remember('ServicesCategories', 10, function () {
            $categoriesTable = (new ServiceCategory())->getTable();
            $servicesTable = (new Service())->getTable();
            $jobsTable = (new Job())->getTable();
            return ServiceCategory::leftJoin($servicesTable, $categoriesTable . '.id', '=', $servicesTable . '.category_id')
                ->leftJoin($jobsTable, $servicesTable . '.id', '=', $jobsTable . '.service_id')
                ->where($jobsTable . '.status', Job::STATUS_PUBLISHED)
                ->orderBy('jobs_count', 'desc')
                ->groupBy($categoriesTable . '.id')
                ->select([$categoriesTable . '.*', DB::raw('COUNT(' . $jobsTable . '.id) AS jobs_count')])
                ->having('jobs_count', '>', 0)
                ->get();
        });

        return view('widgets.services_categories', [
            'config' => $this->config,
            'categories' => $categories,
        ]);
    }
}
