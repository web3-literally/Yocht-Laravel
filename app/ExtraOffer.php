<?php

namespace App;

use App\Models\Vessels\Vessel;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class ExtraOffer
 * @package App
 */
class ExtraOffer extends Model
{
    public $table = 'extra_offers';

    protected $fillable = [
        'status',
        'started_at',
        'paused_at',
        'finished_at',
        'custom',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        //'vessel_id' => 'int',
        'custom' => 'array'
    ];

    /**
     * @return string
     */
    public function getNameTitleAttribute()
    {
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', $this->name);
    }

    /**
     * @return string
     */
    public function getStatusTitleAttribute()
    {
        return mb_convert_case($this->status, MB_CASE_TITLE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return $this
     * @throws \Throwable
     */
    public function setActive() {
        if ($this->getOriginal('status') == 'pause') {
            $diff = strtotime($this->finished_at) - strtotime($this->paused_at);

            $this->fill([
                'status' => 'active',
                'started_at' => $this->paused_at,
                'paused_at' => null,
                'finished_at' => date('Y-m-d H:i:s', time() + $diff)
            ])->saveOrFail();
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Throwable
     */
    public function setPause() {
        if ($this->getOriginal('status') == 'active') {
            $this->fill([
                'status' => 'pause',
                'paused_at' => date('Y-m-d H:i:s')
            ])->saveOrFail();
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Throwable
     */
    public function setFail() {
        $this->fill([
            'status' => 'fail',
            'paused_at' => date('Y-m-d H:i:s')
        ])->saveOrFail();

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function renew() {
        throw new \Exception("No renew method for {$this->name} offer");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id')->withTrashed();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMy($query)
    {
        return $query->where('user_id', Sentinel::getUser()->getUserId());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExtraVessel($query)
    {
        return $query->where('name', 'ExtraVessel');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExtraTender($query)
    {
        return $query->where('name', 'ExtraTender');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExtraTeamMember($query)
    {
        return $query->where('name', 'ExtraTeamMember');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePause($query)
    {
        return $query->where('status', 'pause');
    }
}
