<?php

namespace App\Models\Vessels;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sentinel;

/**
 * Class VesselTransfer
 * @package App\Models\Vessels
 */
class VesselTransfer extends Model
{
    public $table = 'vessel_transfers';

    /**
     * @var array
     */
    public $fillable = [
        'transfer_date'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        $user = Sentinel::getUser();
        return $query->where('origin_member_id', $user->getUserId());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function boat()
    {
        return $this->belongsTo(Vessel::class, 'boat_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origin()
    {
        return $this->belongsTo(User::class, 'origin_member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destination()
    {
        return $this->belongsTo(User::class, 'destination_member_id');
    }
}
