<?php

namespace App\Repositories;

use App\Models\Vessels\Vessel;
use InfyOm\Generator\Common\BaseRepository;

class VesselRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Vessel::class;
    }
}
