<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Blog;
use Sentinel;
use Analytics;
use View;
use Charts;
use App\User;
use Illuminate\Support\Facades\DB;
use Spatie\Analytics\Period;
use File;

/**
 * Class BackEndController
 * @package App\Http\Controllers\Admin
 */
class BackEndController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHome()
    {
        /*$storagePath = storage_path() . '/app/analytics/';
        if (File::exists($storagePath . 'service-account-credentials.json')) {
            //Last week visitors statistics
            $month_visits = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7))->groupBy(function (array $visitorStatistics) {
                return $visitorStatistics['date']->format('Y-m-d');
            })->map(function ($visitorStatistics, $yearMonth) {
                list($year, $month, $day) = explode('-', $yearMonth);
                return ['date' => "{$year}-{$month}-{$day}", 'visitors' => $visitorStatistics->sum('visitors'), 'pageViews' => $visitorStatistics->sum('pageViews')];
            })->values();

            //yearly visitors statistics
            $year_visits = Analytics::fetchTotalVisitorsAndPageViews(Period::days(365))->groupBy(function (array $visitorStatistics) {
                return $visitorStatistics['date']->format('Y-m');
            })->map(function ($visitorStatistics, $yearMonth) {
                list($year, $month) = explode('-', $yearMonth);
                return ['date' => "{$year}-{$month}", 'visitors' => $visitorStatistics->sum('visitors'), 'pageViews' => $visitorStatistics->sum('pageViews')];
            })->values();

            // total page visitors and views
            $visitorsData = Analytics::performQuery(Period::days(7), 'ga:visitors,ga:pageviews', ['dimensions' => 'ga:date']);
            $visitorsData = collect($visitorsData['rows'] ?? [])->map(function (array $dateRow) {
                return [

                    'visitors' => (int)$dateRow[1],
                    'pageViews' => (int)$dateRow[2],
                ];
            });
            $visitors = 0;
            $pageVisits = 0;
            foreach ($visitorsData as $val) {
                $visitors += $val['visitors'];
                $pageVisits += $val['pageViews'];

            }
            $analytics_error = 0;
        } else {
            $month_visits = 0;
            $year_visits = 0;
            $visitors = 0;
            $pageVisits = 0;
            $analytics_error = 1;
        }

        //total users
        $user_count = User::count();
        //total Blogs
        $blog_count = Blog::count();
        $blogs = Blog::orderBy('id', 'desc')->take(5)->get()->load('category', 'author');
        $users = User::orderBy('id', 'desc')->take(5)->get();

        $chart_data = User::select(DB::raw("COUNT(*) as count_row"))
            ->orderBy("created_at")
            ->groupBy(DB::raw("month(created_at)"))
            ->get();
        $db_chart = Charts::database(User::all(), 'area', 'morris')
            ->elementLabel("Users")
            ->dimensions(0, 250)
            ->responsive(true)
            ->groupByMonth(2018, true);


        $countries = DB::table('users')->where('deleted_at', null)
            ->leftJoin('countries', 'countries.shortname', '=', 'users.country')
            ->select('countries.name')
            ->get();
        $geo = Charts::database($countries, 'geo', 'google')
            ->dimensions(0, 250)
            ->responsive(true)
            ->groupBy('name');

        $roles = DB::table('role_users')
            ->join('users', 'users.id', '=', 'role_users.user_id')->wherenull('deleted_at')
            ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
            ->select('roles.name')
            ->get();
        $user_roles = Charts::database($roles, 'pie', 'google')
            ->dimensions(0, 200)
            ->responsive(true)
            ->groupBy('name');
        $line_chart = Charts::database(User::all(), 'donut', 'morris')
            ->elementLabel("Users")
            ->dimensions(0, 150)
            ->responsive(true)
            ->groupByMonth(2017, true);*/

        return view('admin.dashboard'/*, ['analytics_error' => $analytics_error, 'chart_data' => $chart_data, 'blog_count' => $blog_count, 'user_count' => $user_count, 'users' => $users, 'db_chart' => $db_chart, 'geo' => $geo, 'user_roles' => $user_roles, 'blogs' => $blogs, 'visitors' => $visitors, 'pageVisits' => $pageVisits, 'line_chart' => $line_chart, 'month_visits' => $month_visits, 'year_visits' => $year_visits]*/);
    }

}