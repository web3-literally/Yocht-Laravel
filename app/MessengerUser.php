<?php

namespace App;

use App\Models\Traits\FullTextSearchTrait;

class MessengerUser extends User
{
    use FullTextSearchTrait;

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'email',
        'first_name',
        'last_name'
    ];

    /**
     * @return array
     */
    public function getSearchableColumns()
    {
        return $this->searchable;
    }
}
