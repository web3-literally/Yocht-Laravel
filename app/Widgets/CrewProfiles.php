<?php

namespace App\Widgets;

use App\CrewMember;
use App\Helpers\Crew;
use App\Helpers\Owner;
use App\Models\Position;
use Arrilot\Widgets\AbstractWidget;
use Sentinel;
use Cache;

/**
 * @deprecated
 */
class CrewProfiles extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function getOwner()
    {
        return Owner::currentOwner();
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $owner = $this->getOwner();

        $crew = Cache::remember('Member' . Sentinel::getUser()->getUserId() . 'CrewProfilesWidget', 10, function () use ($owner) {
            $memberTable = (new CrewMember())->getTable();
            $positionTable = (new Position())->getTable();

            return CrewMember::join($positionTable, $memberTable . '.position_id', '=', $positionTable . '.id')
                ->where('parent_id', $owner->id)
                ->whereIn($positionTable . '.slug', Crew::IMPORTANT_MEMBERS)
                ->groupBy($memberTable . '.id')
                ->orderBy($positionTable . '.order', 'desc')
                ->select($memberTable . '.*')
                ->get();
        });

        return view('widgets.crew_profiles', [
            'config' => $this->config,
            'crew' => $crew,
        ]);
    }
}
