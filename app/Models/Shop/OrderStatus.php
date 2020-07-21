<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Sentinel;

/**
 * Class OrderStatus
 * @package App\Models\Shop
 */
class OrderStatus extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shop_order_status';

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