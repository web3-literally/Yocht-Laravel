<?php

namespace App;

use App\Models\Position;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselsCrew;

/**
 * Class CrewMember
 * @package App
 */
class CrewMember extends User
{
    /**
     * @var array
     */
    const CREW_ROLES = ['captain', 'crew'];

    /**
     * @deprecated
     * @return string
     */
    public function getPositionLabelAttribute()
    {
        return $this->position_id ? $this->position->label : '-';
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    /**
     * @return Vessel|null
     */
    public function getVesselAttribute()
    {
        if ($this->inCrewOf->count()) {
            return $this->inCrewOf->first();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isInCrew()
    {
        return (bool)$this->inCrewOf()->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inCrewOf()
    {
        $vesselsCrewTable = (new VesselsCrew())->getTable();

        return $this->belongsToMany(Vessel::class, $vesselsCrewTable, 'user_id');
    }
}
