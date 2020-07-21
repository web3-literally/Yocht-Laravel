<?php

namespace App\Http\Controllers;

use App\Country;
use App\Http\Requests\Shop\PlaceOrderRequest;
use App\Mail\Shop\OrderPlaced;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Http\Request;
use Sentinel;
use Shop;
use Mail;
use Illuminate\Support\MessageBag;

/**
 * Class StoreController
 * @package App\Http\Controllers
 */
class StoreController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ShopController constructor.
     * @param MessageBag $messageBag
     * @param ProductRepository $productRepository
     */
    public function __construct(MessageBag $messageBag, ProductRepository $productRepository)
    {
        parent::__construct($messageBag);

        $this->productRepository = $productRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function index()
    {
        $products = Product::inStock()->orderBy('name', 'asc')->paginate(24);

        return view('store.index', compact('products'));
    }

    /**
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function showProduct(string $slug)
    {
        $product = Product::where('url_key', $slug)->first();
        if ($product && $product->isInStock()) {
            return view('store.product', ['product' => $product]);
        }

        return abort(404);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function addProductToCart(int $id, Request $request)
    {
        $product = Product::find($id);
        if (!$product) {
            return abort(404);
        }

        $qty = $request->post('qty', 1);

        Cart::current()->add($product, $qty);

        return redirect(route('store.product', $product))->with('success', 'The "' . $product->name . '" x ' . $qty . ' was added to cart');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function removeProductFromCart(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return abort(404);
        }

        Cart::current()->remove($product);

        return redirect(route('store.cart'))->with('success', 'The "' . $product->name . '" was removed from cart');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCart()
    {
        $cart = Cart::current();

        return view('store.cart', compact('cart'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCheckout()
    {
        $cart = Cart::current();
        if (!$cart->items->count()) {
            return redirect()->route('store.cart');
        }

        $shippingAddress = new \stdClass();
        $shippingAddress->address = Sentinel::getUser()->address;
        $shippingAddress->country = Sentinel::getUser()->country ? Sentinel::getUser()->country : Country::where('shortname', '=', 'US')->first()->id;
        $shippingAddress->state = Sentinel::getUser()->user_state;
        $shippingAddress->city = Sentinel::getUser()->city;
        $shippingAddress->postcode = Sentinel::getUser()->postal;

        $countries = Country::all()->keyBy('id');

        return view('store.checkout', compact('cart', 'countries', 'shippingAddress'));
    }

    /**
     * @param PlaceOrderRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function placeOrder(PlaceOrderRequest $request)
    {
        $cart = Cart::current();
        if (!$cart->items->count()) {
            return redirect()->route('store.cart');
        }

        $fields = [];
        if (empty(Sentinel::getUser()->address)) {
            $fields['address'] = $request->get('shipping_address');
        }
        if (empty(Sentinel::getUser()->country)) {
            $fields['country'] = $request->get('shipping_country');
        }
        if (empty(Sentinel::getUser()->user_state)) {
            $fields['user_state'] = $request->get('shipping_state');
        }
        if (empty(Sentinel::getUser()->city)) {
            $fields['city'] = $request->get('shipping_city');
        }
        if (empty(Sentinel::getUser()->postal)) {
            $fields['postal'] = $request->get('shipping_postcode');
        }
        if ($fields)
            Sentinel::getUser()->update($fields);

        Shop::setGateway('paypalExpressExtended');

        Shop::checkout($cart);

        $order = Shop::placeOrder($cart);

        if (!$order->fill($request->only([
            'shipping_address',
            'shipping_country',
            'shipping_state',
            'shipping_city',
            'shipping_postcode',
        ]))->save()) {
            throw new Exception('Failed to save shipping address', 500);
        }

        Mail::queue(new OrderPlaced($order));

        if ($order->isPending) {
            // PayPal URL to redirect to proceed with payment
            $approvalUrl = Shop::gateway()->getApprovalUrl();
            // Redirect to url
            return redirect($approvalUrl);
        }

        return redirect()->route(config('shop.callback_redirect_route'), ['order' => $order->id]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCheckoutOrder($order)
    {
        $order = Order::find($order);
        if (!$order) {
            return abort(404);
        }

        return view('store.checkout-order', compact('order'));
    }
}
