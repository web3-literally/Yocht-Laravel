<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DashboardMetaTrait;
use Sentinel;

/**
 * Class TransactionsController
 * @package App\Http\Controllers
 */
class TransactionsController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myInvoices()
    {
        resolve('seotools')->metatags()->setTitle(trans('billing.invoices'));

        $invoices = Sentinel::getUser()->invoicesIncludingPending();

        return view('transactions.invoices', compact('invoices'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function downloadInvoice($id)
    {
        return Sentinel::getUser()->downloadInvoice($id, [
            'vendor' => config('app.name'),
            'product' => 'Membership',
        ]);
    }
}
