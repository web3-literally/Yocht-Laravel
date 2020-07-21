<?php

namespace App\Models\Shop;

use Amsgames\LaravelShop\Models\ShopCartModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Sentinel;
use Shop;

/**
 * Class Cart
 * @package App\Models\Shop
 */
class Cart extends ShopCartModel
{
    /**
     * Property used to stored calculations.
     * @var array
     */
    protected $shopCalculations = null;

    /**
     * One-to-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'), 'user_id');
    }

    /**
     * Scope to current user cart.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  Query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCurrent($query)
    {
        if (!Sentinel::check()) return $query;
        return $query->whereUser(Sentinel::getUser()->getUserId())->latest();
    }

    /**
     * Scope to current user cart and returns class model.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  Query.
     *
     * @return this
     */
    public function scopeCurrent($query)
    {
        if (!Sentinel::check()) return;
        $cart = $query->whereCurrent()->first();
        if (empty($cart)) {
            $cart = call_user_func( Config::get('shop.cart') . '::create', [
                'user_id' =>  Sentinel::getUser()->getUserId()
            ]);
        }
        return $cart;
    }

    /**
     * Returns total amount of items in cart.
     *
     * @return int
     */
    public function getCountAttribute()
    {
        if (empty($this->shopCalculations)) $this->runCalculations();
        return round($this->shopCalculations->itemCount, 2);
    }

    /**
     * Returns total price of all the items in cart.
     *
     * @return float
     */
    public function getTotalPriceAttribute()
    {
        if (empty($this->shopCalculations)) $this->runCalculations();
        return round($this->shopCalculations->totalPrice, 2);
    }

    /**
     * Returns total tax of all the items in cart.
     *
     * @return float
     */
    public function getTotalTaxAttribute()
    {
        if (empty($this->shopCalculations)) $this->runCalculations();
        return round($this->shopCalculations->totalTax + ($this->totalPrice * Config::get('shop.tax')), 2);
    }

    /**
     * Returns total tax of all the items in cart.
     *
     * @return float
     */
    public function getTotalShippingAttribute()
    {
        if (empty($this->shopCalculations)) $this->runCalculations();
        return round($this->shopCalculations->totalShipping, 2);
    }

    /**
     * Returns total discount amount based on all coupons applied.
     *
     * @return float
     */
    public function getTotalDiscountAttribute() { /* TODO */ }

    /**
     * Returns total amount to be charged base on total price, tax and discount.
     *
     * @return float
     */
    public function getTotalAttribute()
    {
        if (empty($this->shopCalculations)) $this->runCalculations();
        return $this->totalPrice + $this->totalTax + $this->totalShipping;
    }

    /**
     * Returns formatted total price of all the items in cart.
     *
     * @return string
     */
    public function getDisplayTotalPriceAttribute()
    {
        return Shop::format($this->totalPrice);
    }

    /**
     * Returns formatted total tax of all the items in cart.
     *
     * @return string
     */
    public function getDisplayTotalTaxAttribute()
    {
        return Shop::format($this->totalTax);
    }

    /**
     * Returns formatted total tax of all the items in cart.
     *
     * @return string
     */
    public function getDisplayTotalShippingAttribute()
    {
        return Shop::format($this->totalShipping);
    }

    /**
     * Returns formatted total amount to be charged base on total price, tax and discount.
     *
     * @return string
     */
    public function getDisplayTotalAttribute()
    {
        return Shop::format($this->total);
    }

    /**
     * Runs calculations.
     */
    protected function runCalculations()
    {
        if (!empty($this->shopCalculations)) return $this->shopCalculations;
        $cacheKey = $this->calculationsCacheKey;
        if (Config::get('shop.cache_calculations')
            && Cache::has($cacheKey)
        ) {
            $this->shopCalculations = Cache::get($cacheKey);
            return $this->shopCalculations;
        }
        $this->shopCalculations = DB::table($this->table)
            ->select([
                DB::raw('sum(' . Config::get('shop.item_table') . '.quantity) as itemCount'),
                DB::raw('sum(' . Config::get('shop.item_table') . '.price * ' . Config::get('shop.item_table') . '.quantity) as totalPrice'),
                DB::raw('sum(' . Config::get('shop.item_table') . '.tax * ' . Config::get('shop.item_table') . '.quantity) as totalTax'),
                DB::raw('sum(' . Config::get('shop.item_table') . '.shipping * ' . Config::get('shop.item_table') . '.quantity) as totalShipping')
            ])
            ->join(
                Config::get('shop.item_table'),
                Config::get('shop.item_table') . '.' . ($this->table == Config::get('shop.order_table') ? 'order_id' : 'cart_id'),
                '=',
                $this->table . '.id'
            )
            ->where($this->table . '.id', $this->attributes['id'])
            ->first();
        if (Config::get('shop.cache_calculations')) {
            Cache::put(
                $cacheKey,
                $this->shopCalculations,
                Config::get('shop.cache_calculations_minutes')
            );
        }
        return $this->shopCalculations;
    }

    /**
     * Resets cart calculations.
     */
    protected function resetCalculations ()
    {
        $this->shopCalculations = null;
        if (Config::get('shop.cache_calculations')) {
            Cache::forget($this->calculationsCacheKey);

            $this->runCalculations();
        }
    }
}