<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Shop\Product;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\DataTables;
use Amsgames\LaravelShop\LaravelShop;

/**
 * Class ProductsController
 * @package App\Http\Controllers\Admin
 */
class ProductsController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ProductsController constructor.
     * @param ProductRepository $productRepo
     */
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Products.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->productRepository->pushCriteria(new RequestCriteria($request));

        //$products = $this->productRepository->all();
        $products = [];

        return view('admin.shop.products.index')->with('products', $products);
    }

    /**
     * Show the form for creating a new Product.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.shop.products.create');
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ProductRequest $request)
    {
        $input = $request->all();

        $this->productRepository->create($input);

        Flash::success('Product saved successfully.');

        return redirect(route('admin.shop.products.index'));
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Product not found');

            return abort(404);
        }

        return view('admin.shop.products.edit')->with('product', $product);
    }

    /**
     * Update the specified Product in storage.
     *
     * @param $id
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, ProductRequest $request)
    {
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Product not found');

            return abort(404);
        }

        $this->productRepository->update($request->all(), $id);

        Flash::success('Product updated successfully.');

        return redirect(route('admin.shop.products.index'));
    }

    /**
     * Remove the specified Product from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.products.delete', ['id' => $id]);

        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        Product::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.shop.products.index'))->with('success', Lang::get('message.success.delete'));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $products = Product::get(['id', 'name', 'sku', 'stock', 'price', 'tax', 'url_key', 'updated_at', 'created_at']);

        return DataTables::of($products)->addColumn('link', function (Product $item) {
            return '<a href=' . route('admin.shop.products.edit', ['products' => $item->id]) . '>' . htmlspecialchars($item->name) . '</a>';
        })->editColumn('price', function (Product $item) {
            return LaravelShop::format($item->price);
        })->editColumn('tax', function (Product $item) {
            return $item->tax ? LaravelShop::format($item->tax) : '';
        })->editColumn('updated_at', function (Product $item) {
            return $item->updated_at->diffForHumans();
        })->editColumn('created_at', function (Product $item) {
            return $item->created_at->toFormattedDateString();
        })->addColumn('actions', function (Product $item) {
            $actions = '';
            $actions .= '<a href=' . route('admin.shop.products.edit', ['products' => $item->id]) . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="edit product"></i></a>';
            $actions .= '<a href=' . route('admin.shop.products.confirm-delete', $item->id) . ' data-toggle="modal" data-id="' . $item->id . '" data-target="#delete_confirm"><i class="livicon" data-name="trash" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete product"></i></a>';
            return $actions;
        })->rawColumns(['link', 'actions'])->make(true);
    }
}
