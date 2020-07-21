<?php

namespace App\Models\Vessels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class VesselManufacturer
 * @package App\Models\Vessels
 */
class VesselManufacturer extends Model
{
    use SoftDeletes;

    public $table = 'vessels_manufacturers';


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
        'label' => 'string'
    ];
}
