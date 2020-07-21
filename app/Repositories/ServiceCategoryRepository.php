<?php

namespace App\Repositories;

use App\Models\Services\ServiceCategory;
use InfyOm\Generator\Common\BaseRepository;

class ServiceCategoryRepository extends BaseRepository
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
        return ServiceCategory::class;
    }
}
