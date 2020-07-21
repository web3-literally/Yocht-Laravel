<?php

namespace App\Widgets;

use App\CrewMember;
use App\Helpers\Crew;
use App\Helpers\Vessel;
use App\Models\Position;
use App\Models\Vessels\VesselsCrew;
use Arrilot\Widgets\AbstractWidget;
use Sentinel;
use Cache;

/**
 * @deprecated
 */
class AssignedCrewProfiles extends AbstractWidget
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
        $vessel = Vessel::currentVessel();
        if (!$vessel) {
            return '';
        }

        $crew = Cache::remember('Member' . Sentinel::getUser()->getUserId() . 'Vessel' . $vessel->id . 'CrewProfilesWidget', 10, function () use ($vessel) {
            $memberTable = (new CrewMember())->getTable();
            $vesselsCrewTable = (new VesselsCrew())->getTable();
            $positionTable = (new Position())->getTable();

            return CrewMember::join($vesselsCrewTable, $memberTable . '.id', '=', $vesselsCrewTable . '.user_id')
                ->join($positionTable, $memberTable . '.position_id', '=', $positionTable . '.id')
                ->where($vesselsCrewTable . '.vessel_id', $vessel->id)
                ->where($memberTable . '.id', '!=', Sentinel::getUser()->getUserId())
                ->whereIn($positionTable . '.slug', Crew::IMPORTANT_MEMBERS)
                ->groupBy($memberTable . '.id')
                ->orderBy($positionTable . '.order', 'desc')
                ->select($memberTable . '.*')
                ->get();
        });

        return view('widgets.assigned_crew_profiles', [
            'config' => $this->config,
            'crew' => $crew,
        ]);
    }
}
