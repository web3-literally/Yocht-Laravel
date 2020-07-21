<?php

namespace App\Http\Controllers;

use App\Events\Member\Subscription\Created;
use App\ExtraOffer;
use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\Http\Requests\SubscriptionRequest;
use App\Jobs\Index\MembersUpdate;
use App\Repositories\ExtraCrewOfferRepository;
use App\Subscription;
use App\Plan;
use App\User;
use DB;
use Sentinel;
use Event as AppEvent;

/**
 * Class SubscriptionsController
 * @package App\Http\Controllers
 */
class SubscriptionsController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mySubscriptions()
    {
        resolve('seotools')->metatags()->setTitle(trans('billing.subscriptions'));

        $subscriptions = Subscription::my()->paginate(5, ['*'], 'subscriptions-page');

        $offers = ExtraOffer::my()->paginate(5, ['*'], 'offers-page');

        return view('subscriptions.subscriptions', compact('subscriptions', 'offers'));
    }

    /**
     * @param SubscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SubscriptionRequest $request)
    {
        // get the plan after submitting the form
        $plan = Plan::findOrFail($request->plan);

        if ($plan->active) {
            // subscribe the user
            $subscription = $request->user()->newSubscription('Membership', $plan->braintree_plan)->create($request->payment_method_nonce, [
                'email' => Sentinel::getUser()->email,
                'firstName' => Sentinel::getUser()->first_name,
                'lastName' => Sentinel::getUser()->last_name
            ]);

            $subscription = Subscription::findOrFail($subscription->id);

            // Update search index
            $user = User::findOrFail($request->user()->getUserId());
            $user->with('subscriptions');
            MembersUpdate::dispatch($user)
                ->onQueue('high');

            AppEvent::fire(new Created($subscription));

            return redirect()->route('subscriptions')->with('success', trans('billing.message.subscription.success'));
        }
        return abort(404);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resumeSubscription(int $id)
    {
        $subscription = Subscription::my()->find($id);
        if (!$subscription) {
            return abort(404);
        }

        if (!$subscription->cancelled()) {
            return redirect(route('subscriptions'))->with('error', 'Your subscription is active.');
        }

        $subscription->resume();

        return redirect(route('subscriptions'))->with('success', 'Your subscription resumed.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(int $id)
    {
        $subscription = Subscription::my()->find($id);
        if (!$subscription) {
            return abort(404);
        }

        $subscription->cancel();

        return redirect(route('subscriptions'))->with('success', 'Your subscription cancelled.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshExtraOffer(int $id)
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->where('status', 'fail')->find($id);
        if (!$offer) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            if ($offer->name == 'ExtraTeamMember') {
                /** @var ExtraCrewOfferRepository $extraOfferRepository */
                $extraOfferRepository = resolve('App\Repositories\ExtraCrewOfferRepository');
                $extraOfferRepository->renewExtraCrewMember($offer);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);

            return redirect(route('subscriptions'))->with('error', 'Failed to refresh extra offer.');
        }

        return redirect(route('subscriptions'))->with('success', 'Your extra offer refreshed.');
    }
}
