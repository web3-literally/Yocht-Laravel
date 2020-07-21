<?php

namespace App\Widgets;

use App\Models\Classifieds\Classifieds;
use Arrilot\Widgets\AbstractWidget;
use Cache;
use DB;

class ClassifiedsBrandsCounts extends AbstractWidget
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

        $brands = Cache::remember('ClassifiedsBrandsAll_' . $this->config['type'], 3, function () {
            return DB::table('classifieds_manufacturers')
                ->leftJoin('classifieds', 'classifieds_manufacturers.id', '=', 'classifieds.manufacturer_id')
                ->where('classifieds_manufacturers.type', $this->config['type'])
                ->whereNull('classifieds.deleted_at')
                ->where('classifieds.status', 'approved')
                ->where('classifieds.type', $this->config['type'])
                ->orderBy('classifieds_manufacturers.title')
                ->groupBy('classifieds_manufacturers.title')
                ->select(['classifieds_manufacturers.title AS manufacturer', DB::raw('COUNT(classifieds.id) AS classifieds_count')])
                ->get();
        });

        return view('widgets.classifieds_brands_counts', [
            'config' => $this->config,
            'brands' => $brands,
            'type' => $this->config['type']
        ]);
    }
}
