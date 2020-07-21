<?php

namespace App\Repositories;

use App\Models\Specialization;
use InfyOm\Generator\Common\BaseRepository;

class SpecializationRepository extends BaseRepository
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
        return Specialization::class;
    }
}
