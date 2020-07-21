<?php

namespace App\Widgets;

use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

class VesselCrewProfiles extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \App\Models\Vessels\Vessel
     */
    public function getVessel()
    {
        $vessel = Vessel::currentVessel();
        if (is_null($vessel)) {
            return null;
        }

        if ($vessel->parent_id) {
            return $vessel->parent;
        }

        return $vessel;
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $vessel = $this->getVessel();
        if (!$vessel) {
            return '';
        }

        $crew = $vessel->crew;

        return view('widgets.vessel_crew_profiles', [
            'config' => $this->config,
            'vessel' => $vessel,
            'crew' => $crew,
        ]);
    }
}
