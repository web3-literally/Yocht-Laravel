<?php

namespace App\Models\Vessels;

use App\CrewMember;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VesselsCrew
 * @package App\Models\Classifieds
 */
class VesselsCrew extends Model
{
    public $timestamps = false;

    public $table = 'vessels_crew';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'owner_id' => 'int',
        'vessel_id' => 'int',
        'user_id' => 'int'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(CrewMember::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(CrewMember::class, 'user_id');
    }
}
