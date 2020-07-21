<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\Http\Requests\PaymentMethodAdd;
use App\User;
use Sentinel;

/**
 * Class PaymentMethodsController
 * @package App\Http\Controllers
 */
class PaymentMethodsController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myPaymentMethods()
    {
        resolve('seotools')->metatags()->setTitle(trans('billing.payment_methods'));

        /** @var User $user */
        $user = Sentinel::getUser();
        $paymentMethods = $user->asBraintreeCustomer()->paymentMethods;
        $paymentMethods = collect($paymentMethods)->sortBy('default', SORT_REGULAR, true);

        return view('payment-methods.payment-methods', compact('paymentMethods'));
    }

    public function myPaymentMethodAdd() {
        /** @var User $user */
        $user = Sentinel::getUser();

        return view('payment-methods.payment-methods-add', compact($user));
    }

    public function myPaymentMethodStore(PaymentMethodAdd $request) {
        /** @var User $user */
        $user = Sentinel::getUser();

        $result = \Braintree_PaymentMethod::create([
            'customerId' => $user->asBraintreeCustomer()->id,
            'paymentMethodNonce' => $request->payment_method_nonce
        ]);

        if (!$result->success) {
            return redirect()->back()->withInput()->with('error', 'Failed to add payment method.');
        }

        return redirect()->route('payment-methods')->with('success', 'Payment method was successfully added.');
    }

    public function myPaymentMethodDelete($token) {
        /** @var User $user */
        $user = Sentinel::getUser();

        $paymentMethods = $user->asBraintreeCustomer()->paymentMethods;
        if (count($paymentMethods) > 1) {
            $paymentMethod = \Braintree_Configuration::gateway()->paymentMethod()->find($token);
            if ($user->asBraintreeCustomer()->id === $paymentMethod->customerId) {
                $result = \Braintree_Configuration::gateway()->paymentMethod()->delete($token);
                if ($result->success) {
                    return redirect()->route('payment-methods')->with('success', 'Payment method was successfully deleted.');
                }
                abort(500);
            }
        }

        abort(404);
    }
}
