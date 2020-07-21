<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\OrderStatusRequest;
use App\Models\Shop\Order;
use App\Models\Shop\OrderStatus;
use Illuminate\Http\Request;
use Flash;
use Response;
use Yajra\DataTables\DataTables;
use Amsgames\LaravelShop\LaravelShop;

/**
 * Class OrdersController
 * @package App\Http\Controllers\Admin
 */
class OrdersController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $statuses = OrderStatus::all();

        return view('admin.shop.orders.index', compact('statuses'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function edit($id)
    {
        $statuses = OrderStatus::all()->keyBy('code');
        $order = Order::find($id);

        if (empty($order)) {
            Flash::error('Product not found');

            return abort(404);
        }

        return view('admin.shop.orders.edit', compact('order', 'statuses'));
    }

    /**
     * @param $id
     * @param OrderStatusRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function updateStatus($id, OrderStatusRequest $request)
    {
        /* @var Order $order */
        $order = Order::find($id);

        if (empty($order)) {
            Flash::error('Product not found');

            return abort(404);
        }

        $order->update([
            'statusCode' => $request->get('statusCode')
        ]);

        Flash::success('Order status updated successfully.');

        return redirect(route('admin.shop.orders.edit', $order->id));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $orders = Order::selectRaw('shop_orders.*, SUM(shop_items.quantity) AS quantity')->join('shop_items', 'shop_items.order_id', '=', 'shop_orders.id')->groupBy('shop_orders.id')->get();

        return DataTables::of($orders)->editColumn('id', function (Order $item) {
            return '<a href=' . route('admin.shop.orders.edit', ['id' => $item->id]) . '>' . htmlspecialchars($item->id) . '</a>';
        })->editColumn('user_id', function (Order $item) {
            return $item->user->full_name;
        })->editColumn('statusCode', function (Order $item) {
            return $item->status->name;
        })->addColumn('items', function (Order $item) {
            return $item->quantity;
        })->addColumn('tax', function (Order $item) {
            return $item->displayTotalTax;
        })->addColumn('shipping', function (Order $item) {
            return  $item->displayTotalShipping;
        })->addColumn('amount', function (Order $item) {
            return $item->displayTotal;
        })->editColumn('updated_at', function (Order $item) {
            return $item->updated_at->diffForHumans();
        })->editColumn('created_at', function (Order $item) {
            return $item->created_at->toFormattedDateString();
        })->addColumn('actions', function (Order $item) {
            $actions = '';
            $actions .= '<a href=' . route('admin.shop.orders.edit', $item->id) . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="edit order"></i></a>';
            return $actions;
        })->rawColumns(['id', 'actions'])->make(true);
    }
}
