<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class Country extends Model
{
    use Eloquence;


    protected $table = 'countries';
    protected $guarded  = ['id'];
    protected $searchableColumns = ['name'];

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
