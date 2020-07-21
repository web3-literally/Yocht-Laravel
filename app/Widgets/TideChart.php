<?php

namespace App\Widgets;

use App\Facades\GeoLocation;
use App\Facades\WorldWeatherOnline;
use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;
use Charts;

/**
 * Class TideChart
 * @package App\Widgets
 */
class TideChart extends AbstractWidget
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
        $location = '-';

        $geoPosition = null;

        $currentVessel = Vessel::currentVessel();

        if ($currentVessel && $currentVessel->location) { // TODO: Event/Listener implementation
            $geoPosition = WorldWeatherOnline::searchLocation($currentVessel->location);
        } elseif (GeoLocation::isDetected()) {
            $geoPosition = WorldWeatherOnline::searchLocation(GeoLocation::getCurrentLocation());
        }

        $labels = [];
        $dataset = [];

        if ($geoPosition) {
            $forecast = WorldWeatherOnline::getForecast($geoPosition);
            if ($forecast) {
                foreach ($forecast as $day) {
                    $tides = current($day->tides)->tide_data;
                    foreach ($tides as $tide) {
                        $labels[] = (new \DateTime($tide->tideDateTime))->format('d H:i');
                        $dataset[] = doubleval($tide->tideHeight_mt);
                    }
                }
                $location = implode(', ', [current($geoPosition->areaName)->value, current($geoPosition->country)->value]);
            }
        }

        /** @var \ConsoleTVs\Charts\Builder\Multi $chart */
        $chart = Charts::multi('area', 'morris')
            ->dimensions(0, 216)
            ->colors(['#00c8e7'])
            ->dataset($location, $dataset)
            ->labels($labels);
        $chart->view = 'morris.multi.area';

        return view('widgets.tide_chart', [
            'config' => $this->config,
            'chart' => $chart,
            'location' => $location
        ]);
    }
}
