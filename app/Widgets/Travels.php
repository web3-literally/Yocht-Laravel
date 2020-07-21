<?php

namespace App\Widgets;

use App\Helpers\Vessel as VesselHelper;
use Arrilot\Widgets\AbstractWidget;

/**
 * Class Travels
 * @package App\Widgets
 */
class Travels extends AbstractWidget
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
        $this->config['key'] = config('services.google_map.key');

        $currentVessel = VesselHelper::currentVessel();

        if (is_null($currentVessel)) {
            return null;
        }

        $rows = $currentVessel->locationHistory()->orderBy('created_at', 'desc')->get();
        $history = $rows->map(function ($item) {
            return [
                'id' => $item->id,
                'address' => $item['address'],
                'map_lat' => $item['map_lat'],
                'map_lng' => $item['map_lng'],
            ];
        });
        if ($currentVessel->address) {
            $history->prepend([
                'address' => $currentVessel->address,
                'map_lat' => $currentVessel->map_lat,
                'map_lng' => $currentVessel->map_lng,
            ]);
        }

        return view('widgets.travels', [
            'config' => $this->config,
            'history' => $history,
        ]);
    }
}
