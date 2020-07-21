<?php

namespace App\Http\Controllers;

use App\Models\Shop\Order;
use Sentinel;

/**
 * Class OrdersController
 * @package App\Http\Controllers
 */
class OrdersController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrder($id)
    {
        $order = Order::my()->find($id);
        if (!$order) {
            return abort(404);
        }

        return view('user_order', compact('order'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myOrders() {
        $orders = Order::my()->paginate(20);

        return view('user_orders', compact('orders'));
    }
}
