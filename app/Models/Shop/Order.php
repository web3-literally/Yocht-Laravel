<?php

namespace App\Models\Shop;

use Amsgames\LaravelShop\Models\ShopOrderModel;
use Sentinel;

/**
 * Class Order
 * @package App\Models\Shop
 */
class Order extends ShopOrderModel
{
    /**
     * One-to-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * One-to-One relations with the status model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'statusCode', 'code');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('created_at', 'desc')->where('user_id', $user->getUserId());
    }
}