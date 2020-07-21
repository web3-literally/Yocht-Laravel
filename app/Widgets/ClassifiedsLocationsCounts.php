<?php

namespace App\Widgets;

use App\Models\Classifieds\Classifieds;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

class ClassifiedsLocationsCounts extends AbstractWidget
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

        $locations = Cache::remember('ClassifiedsLocationsAll_' . $this->config['type'], 3, function () {
            return DB::table('classifieds')
                ->whereNull('classifieds.deleted_at')
                ->where('classifieds.status', 'approved')
                ->where('classifieds.type', $this->config['type'])
                ->orderBy('classifieds.state_province')
                ->groupBy('classifieds.state_province')
                ->select(['classifieds.state_province AS state_province', DB::raw('COUNT(classifieds.id) AS classifieds_count')])
                ->get();
        });

        return view('widgets.classifieds_locations_counts', [
            'config' => $this->config,
            'locations' => $locations,
            'type' => $this->config['type']
        ]);
    }
}
