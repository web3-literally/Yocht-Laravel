<?php

namespace App\Models\Shop;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Amsgames\LaravelShop\Traits\ShopItemTrait;

/**
 * Class Product
 * @package App\Models\Shop
 */
class Product extends Model
{
    use Sluggable;
    use ShopItemTrait, SoftDeletes;

    public $table = 'shop_products';

    protected $itemName = 'name';

    protected $dates = ['deleted_at'];

    /**
     * Name of the route to generate the item url.
     *
     * @var string
     */
    protected $itemRouteName = 'product';

    /**
     * Name of the attributes to be included in the route params.
     *
     * @var string
     */
    protected $itemRouteParams = ['url_key'];

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'description',
        'sku',
        'stock',
        'price',
        'tax',
        'url_key'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'url_key' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'sku' => 'string',
        'stock' => 'integer',
        'url_key' => 'string'
    ];

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'url_key';
    }

    public function isInStock()
    {
        return boolval($this->stock > 0 || is_null($this->stock));
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0)->orWhereNull('stock');
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        return parent::save($options);
    }
}