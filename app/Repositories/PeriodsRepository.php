<?php

namespace App\Repositories;

use App\Models\Jobs\Job;
use App\Models\Jobs\Period;
use InfyOm\Generator\Common\BaseRepository;
use DB;

class PeriodsRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Period::class;
    }

    /**
     * @param int $boat_id
     * @param callable|null $callback
     * @return array
     */
    public function getMyPeriodsDropdown($boat_id, callable $callback = null)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = with(clone Job::my($boat_id));

        $builder->getQuery()->orders = $builder->getQuery()->groups = $builder->getQuery()->columns = [];
        $builder
            ->join('jobs_periods', 'jobs.id', '=', 'jobs_periods.job_id')
            ->join('job_periods', 'jobs_periods.period_id', '=', 'job_periods.id')
            ->orderBy('job_periods.id', 'desc')
            ->groupBy('job_periods.id')
            ->select(['job_periods.id', DB::raw('CONCAT(job_periods.name, " ", lpad(job_periods.month, 2, 0), "/", job_periods.year, " (", job_periods.period_type, ")") AS title')]);

        if ($callback) {
            call_user_func($callback, $builder);
        }

        $periods = $builder->pluck('job_periods.title', 'job_periods.id');

        $search = array_reverse(array_keys(Period::getPeriodTypes()));
        $replace = array_reverse(array_values(Period::getPeriodTypes()));
        $periods = $periods->map(function ($title) use ($search, $replace) {
            return str_replace($search, $replace, $title);
        })->all();

        return $periods;
    }

    /**
     * @param int $boat_id
     * @param callable|null $callback
     * @return array|null
     */
    public function getOpenedPeriod($boat_id, callable $callback = null)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = DB::table(Period::getModel()->getTable() . ' AS t')
            ->where('t.vessel_id', $boat_id)
            ->orderBy('t.id', 'desc')
            ->select(['t.id', DB::raw('CONCAT(t.name, " ", lpad(t.month, 2, 0), "/", t.year, " (", t.period_type, ")") AS title')]);

        if ($callback) {
            call_user_func($callback, $builder);
        }

        $period = $builder->limit(1)->first();
        if (empty($period)) {
            return null;
        }

        $search = array_reverse(array_keys(Period::getPeriodTypes()));
        $replace = array_reverse(array_values(Period::getPeriodTypes()));

        return [$period->id => str_replace($search, $replace, $period->title)];
    }
}
