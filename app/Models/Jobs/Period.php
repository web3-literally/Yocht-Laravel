<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Class Period
 * @package App\Models\Jobs
 */
class Period extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;

    /**
     * @var string
     */
    public $table = 'job_periods';

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'shipyard_name',
        'month',
        'year',
        'period_type',
        'from',
        'to',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['year', 'month', 'period_type', 'name']
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getPeriodTypes()
    {
        return [
            'yard_period' => 'Yard Period',
            'emergancy_yard_period' => 'Emergancy Yard Period',
            'refit_period' => 'Refit Period'
        ];
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        $month = sprintf('%02.d', $this->month);
        return "{$this->name} {$month}/{$this->year}";
    }

    /**
     * @return string
     */
    public function getPeriodLabelAttribute()
    {
        $labels = $this->getPeriodTypes();
        return $labels[$this->period_type] ?? '';
    }
}