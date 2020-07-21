<?php

namespace App\Repositories;

use App\Models\MenuItem;
use InfyOm\Generator\Common\BaseRepository;

class MenuItemRepository extends BaseRepository
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
        return MenuItem::class;
    }
}
