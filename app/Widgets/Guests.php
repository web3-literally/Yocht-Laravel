<?php

namespace App\Widgets;

use App\User;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Activitylog\Models\Activity;
use Sentinel;
use Cache;
use DB;

/**
 * Class Guests
 * @package App\Widgets
 */
class Guests extends AbstractWidget
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
        $user = Sentinel::getUser();

        $guests = Cache::remember('MemberPageViewed' . $user->getUserId(), 10, function () use ($user) {
            $limit = 8;

            $ids = Activity::where('log_name', 'Member Visited')
                ->where('subject_id', $user->getUserId())
                ->orderBy('created_at', 'desc')
                ->groupBy('causer_id')
                ->limit($limit)
                ->pluck('causer_id')
                ->all();

            if ($ids) {
                $guests = User::whereIn('id', $ids)
                    ->limit($limit)
                    ->orderBy(DB::raw('FIELD(id, ' . implode(',', $ids) . ')'))
                    ->get();
            } else {
                $guests = new Collection();
            }

            return $guests;
        });

        return view('widgets.guests', [
            'config' => $this->config,
            'guests' => $guests,
        ]);
    }
}
