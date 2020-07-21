<?php

namespace App\Models\Vessels;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationHistory
 * @package App\Models\Vessels
 */
class LocationHistory extends Model
{
    public $timestamps = false;

    public $table = 'location_history';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'vessel_id' => 'int',
        'address' => 'string',
        'map_lat' => 'string',
        'map_lng' => 'string',
    ];

    protected $dates = [
        'created_at'
    ];

    public $fillable = [
        'address',
        'map_lat',
        'map_lng',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }
}
