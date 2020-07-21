<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class Currency extends Model
{
    use Eloquence;


    protected $table = 'currencies';
    protected $guarded  = ['id'];
    protected $searchableColumns = ['code'];

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->symbol_native;
    }
}
