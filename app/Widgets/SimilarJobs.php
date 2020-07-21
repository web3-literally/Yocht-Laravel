<?php

namespace App\Widgets;

use App\Models\Jobs\Job;
use App\Models\Jobs\JobServices;
use Arrilot\Widgets\AbstractWidget;
use Cache;

/**
 * Class SimilarJobs
 * @package App\Widgets
 */
class SimilarJobs extends AbstractWidget
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
        $slug = request('slug');
        if (!$slug) {
            return '';
        }
        $job = Job::where('slug', $slug)->first();
        if (!$job) {
            return '';
        }

        $similarJobs = Cache::remember('SimilarJobs' . $job->id, 60, function () use ($job) {
            $jobsTable = $job->getTable();
            $servicesTable = (new JobServices())->getTable();

            $similarJobs = Job::onlyPublicIndex()
                ->leftJoin($servicesTable, $servicesTable . '.job_id', '=', $jobsTable . '.id')
                ->whereIn($servicesTable.'.service_id', $job->services->pluck('id')->all())
                ->where($jobsTable . '.id', '!=', $job->id)
                ->groupBy($jobsTable . '.id')
                ->select($jobsTable . '.*')
                ->limit(3)
                ->get();

            return $similarJobs;
        });

        return view('widgets.similar_jobs', [
            'config' => $this->config,
            'jobs' => $similarJobs
        ]);
    }
}
