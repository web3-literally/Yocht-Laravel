<?php

namespace App\Repositories;

use App\Models\Vessels\VesselManufacturer;
use InfyOm\Generator\Common\BaseRepository;

class VesselManufacturerRepository extends BaseRepository
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
        return VesselManufacturer::class;
    }
}
