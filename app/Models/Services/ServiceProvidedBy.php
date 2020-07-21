<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;
use Rutorika\Sortable\SortableTrait;

/**
 * Class ServiceProvidedBy
 * @package App\Models\Services
 */
class ServiceProvidedBy extends Model
{
    use SortableTrait;

    public $timestamps = false;

    public $table = 'services_provided_by';

    public $fillable = [
        'position'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'position' => 'int'
    ];

    protected static $sortableGroupField = ['category_id', 'parent_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(ServiceGroup::class, 'group_id', 'id');
    }
}
