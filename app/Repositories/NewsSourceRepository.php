<?php

namespace App\Repositories;

use App\Models\NewsSource;
use InfyOm\Generator\Common\BaseRepository;

class NewsSourceRepository extends BaseRepository
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
        return NewsSource::class;
    }
}
