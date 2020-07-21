<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'shop_products';

    protected $fillable = [];

    protected $guarded = ['id'];
}
