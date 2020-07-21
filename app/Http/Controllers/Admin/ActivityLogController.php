<?php

namespace App\Http\Controllers\Admin;

use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

/**
 * Class ActivityLogController
 * @package App\Http\Controllers\Admin
 */
class ActivityLogController extends BackEndController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.activity_log');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function activityLogData()
    {
        $logs = Activity::get(['causer_id', 'log_name', 'description', 'created_at']);
        return DataTables::of($logs)
            ->editColumn('created_at', function (Activity $item) {
                return $item->created_at->toFormattedDateString();
            })->make(true);
    }
}