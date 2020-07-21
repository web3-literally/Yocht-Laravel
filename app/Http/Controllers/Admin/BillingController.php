<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Plan;
use App\Subscription;
use App\User;
use Yajra\DataTables\DataTables;

/**
 * Class BillingController
 * @package App\Http\Controllers\Admin
 */
class BillingController extends BackEndController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function plans(Request $request)
    {
        $plans = Plan::active()->orderBy('billing_frequency', 'asc')->orderBy('name', 'asc')->get();

        return view('admin.billing.plans', compact('plans'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscriptions(Request $request)
    {
        return view('admin.billing.subscriptions');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function subscriptionsData()
    {
        $subscriptions = Subscription::orderBy('created_at', 'desc')->get(['id', 'braintree_id', 'name', 'braintree_plan', 'trial_ends_at', 'ends_at', 'created_at']);

        return DataTables::of($subscriptions)->addColumn('status', function (Subscription $subscription) {
            return $subscription->active() ? 'Active' : '';
        })->editColumn('braintree_plan', function (Subscription $subscription) {
            return $subscription->plan()->first()->name;
        })->editColumn('trial_ends_at', function (Subscription $subscription) {
            return is_null($subscription->trial_ends_at) ? '' : $subscription->trial_ends_at->toFormattedDateString();
        })->editColumn('ends_at', function (Subscription $subscription) {
            return is_null($subscription->ends_at) ? '' : $subscription->ends_at->toFormattedDateString();
        })->editColumn('created_at', function (Subscription $subscription) {
            return $subscription->created_at->toFormattedDateString();
        })->addColumn('actions', function (Subscription $subscription) {
            $actions = '';
            if ($subscription->active()) {
                $actions .= '<a href="' . route('admin.billing.subscriptions.cancel', $subscription->id) . '" onclick="return confirm(\'Are you sure to cancel subscription? This operation is irreversible.\')">Cancel Now</a>';
            }
            return $actions;
        })->rawColumns(['actions'])->make(true);
    }

    public function subscriptionCancelNow(int $id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return abort(404);
        }

        $subscription->cancelNow();

        return redirect(route('admin.billing.subscriptions'))->with('success', 'The subscription ' . $subscription->braintree_id . ' cancelled.');
    }

    /**
     * @param int $user
     * @return mixed
     * @throws \Exception
     */
    public function subscriptionsUserData($user)
    {
        $user = User::findOrFail($user);

        $subscriptions = Subscription::where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->get(['braintree_id', 'name', 'braintree_plan', 'trial_ends_at', 'ends_at', 'created_at']);

        return DataTables::of($subscriptions)->addColumn('status', function (Subscription $subscription) {
            return $subscription->active() ? 'Active' : '';
        })->editColumn('braintree_plan', function (Subscription $subscription) {
            return $subscription->plan()->first()->name;
        })->editColumn('trial_ends_at', function (Subscription $subscription) {
            return is_null($subscription->trial_ends_at) ? '' : $subscription->trial_ends_at->toFormattedDateString();
        })->editColumn('ends_at', function (Subscription $subscription) {
            return is_null($subscription->ends_at) ? '' : $subscription->ends_at->toFormattedDateString();
        })->editColumn('created_at', function (Subscription $subscription) {
            return $subscription->created_at->toFormattedDateString();
        })->make(true);
    }
}
