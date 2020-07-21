<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Specialization extends Model
{
    use SoftDeletes;

    public $table = 'specializations';
    

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
