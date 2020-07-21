<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ServiceGroup
 * @package App\Models\Services
 */
class ServiceGroup extends Model
{
    use SoftDeletes;

    public $table = 'services_groups';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'label'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'label' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(ServiceCategory::class, 'group_id', 'id');
    }

    /**
     * @param Builder $query
     * @param $provided_by
     * @return Builder
     */
    public function scopeProvidedBy(Builder $query, $provided_by)
    {
        $providedByTable = ServiceProvidedBy::getModel()->getTable();
        return $query->join($providedByTable, $this->getTable() . '.id', $providedByTable . '.group_id')
            ->where($providedByTable . '.provided_by', $provided_by)
            ->orderBy($providedByTable . '.position', 'asc')
            ->select($this->getTable() . '.*');
    }
}
