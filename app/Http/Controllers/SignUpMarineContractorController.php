<?php

namespace App\Http\Controllers;

use App\Helpers\Geocoding;
use App\Events\Member\Subscription\Created;
use App\Http\Requests\SignUp\SignUpMarineContractorBusinessAccountRequest;
use App\Http\Requests\SignUp\SignUpOwnerAccountRequest;
use App\Http\Requests\SignUp\SignUpMarineContractorPaymentInfoRequest;
use App\Mail\SignUp\MemberActivation;
use App\Models\Business\Business;
use App\Models\Services\ServiceGroup;
use App\Plan;
use App\Profile;
use App\Repositories\ServiceRepository;
use App\Role;
use App\Subscription;
use App\User;
use Event as AppEvent;
use Illuminate\Support\MessageBag;
use Sentinel;
use Mail;
use DB;

/**
 * Class SignUpMarineContractorController
 * @package App\Http\Controllers
 */
class SignUpMarineContractorController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * SignUpMarinasShipyardsController constructor.
     * @param MessageBag $messageBag
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(MessageBag $messageBag, ServiceRepository $serviceRepository)
    {
        parent::__construct($messageBag);

        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signupOwnerMarineContractorAccount()
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        return view('signup.marine-contractor-sign-up.owner-marine-contractor-sign-up');
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function storeOwnerMarineContractorAccount(SignUpOwnerAccountRequest $request)
    {
        DB::beginTransaction();

        try {
            // Register a marine contractor account
            $data = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'country' => $request->get('country'),
                'user_state' => $request->get('user_state'),
                'city' => $request->get('city'),
                'address' => $request->get('address'),
                'password' => SignUpController::generateRandomString() // Generate temporary password
            ];

            //TODO: Optimize duplicate code
            $address = trim("{$data['address']} {$data['city']} {$data['user_state']} {$data['country']}");
            $response = Geocoding::latlngLookup($address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = current($response->results);
                    $data['map_lat'] = $place->geometry->location->lat;
                    $data['map_lng'] = $place->geometry->location->lng;
                }
            }

            /** @var User $user */
            $user = Sentinel::register($data, false);

            // Add role for user
            /** @var Role $role */
            $role = Sentinel::findRoleBySlug('marine');
            $role->users()->attach($user);

            // Make a Customer
            $customer = $user->createAsBraintreeCustomer();
            $user->saveOrFail();

            // Make a profile
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->fill($request->get('profile', []));
            $profile->save();

            // Sending activation email
            $mail = new MemberActivation($user);
            Mail::send($mail);

            DB::commit();

            session(['signup-id' => $user->id]);
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if (isset($customer)) {
                $user->deleteBraintreeCustomer();
            }

            return redirect()->route('signup.owner-marine-contractor-account')->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect()->route('signup.owner-marine-contractor-account.business-info', ['id' => session()->get('signup-id')]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function signupBusinessAccount(int $id)
    {
        if ($id && session()->get('signup-id') === $id) {
            /** @var User $owner */
            $owner = User::findOrFail($id);
            if ($owner->hasBusiness()) {
                return redirect()->route('signup.owner-marine-contractor-account.payment-info', ['id' => session()->get('signup-id')]);
            }

            resolve('seotools')->setTitle(trans('general.sign_up'));

            $serviceGroups = ServiceGroup::providedBy($owner->getAccountType())->pluck('label', 'id');

            return view('signup.marine-contractor-sign-up.owner-marine-contractor-business-info', compact('id', 'serviceGroups'));
        }

        return redirect()->route('signup.owner-marine-contractor-account.payment-info', ['id' => session()->get('signup-id')]);
    }

    /**
     * @param int $id
     * @param SignUpMarineContractorBusinessAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBusinessAccount(int $id, SignUpMarineContractorBusinessAccountRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            DB::beginTransaction();

            try {
                /** @var User $owner */
                $owner = User::findOrFail($id);

                $role = Sentinel::findRoleBySlug('business');

                $i = 1;
                foreach($request->get('business') as $business) {
                    // Register a business account
                    /** @var User $user */
                    $user = Sentinel::register([
                        'parent_id' => $owner->id,
                        'email' => uniqid('business_') . '@' . $_SERVER['SERVER_NAME'],
                        'password' => SignUpController::generateRandomString()
                    ], false);
                    $role->users()->attach($user);

                    // Make a profile
                    $model = new Business();
                    $model->business_type = $owner->getAccountType();
                    $model->fill($business);
                    $model->is_primary = ($i == 1);
                    $model->user_id = $user->id;
                    $model->owner_id = $owner->id;
                    $address = trim("{$business['company_address']} {$business['company_city']}");
                    $response = Geocoding::latlngLookup($address);
                    if ($response && $response->status === 'OK') {
                        if ($response->results) {
                            $place = current($response->results);
                            $model->map_lat = $place->geometry->location->lat;
                            $model->map_lng = $place->geometry->location->lng;
                        }
                    }

                    $model->saveOrFail();

                    // Business Specialization
                    $user->categories()->attach($business['categories']);
                    $user->services()->attach($business['services'] ?? []);

                    $user->addToIndex();

                    $i++;
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();

                report($e);

                $request->session()->flash('error', trans('auth/message.signup.error'));

                return redirect()->route('signup.owner-marine-contractor-account.business-info', ['id' => session()->get('signup-id')])->withInput();
            }
        }

        return redirect()->route('signup.owner-marine-contractor-account.payment-info', ['id' => session()->get('signup-id')]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function paymentInfo(int $id)
    {
        if ($id && session()->get('signup-id') === $id) {
            resolve('seotools')->setTitle(trans('general.sign_up'));

            $plans = Plan::active()->where('slug', 'like', "%marine%")->orderBy('billing_frequency', 'desc')->get();

            return view('signup.marine-contractor-sign-up.owner-marine-contractor-payment-info', compact('id', 'plans'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @param SignUpMarineContractorPaymentInfoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentInfoStore(int $id, SignUpMarineContractorPaymentInfoRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            try {
                $user = User::findOrFail($id);

                // get the plan after submitting the form
                $plan = Plan::active()->where('slug', 'like', "%marine%")->findOrFail($request->plan);

                if (!$plan->active) {
                    throw new \Exception('Plan is not active', 500);
                }

                // subscribe the user
                $subscription = $user->newSubscription('Membership', $plan->braintree_plan)->create($request->payment_method_nonce, [
                    'email' => $user->email,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name
                ]);

                /** @var Subscription $subscription */
                $subscription = Subscription::findOrFail($subscription->id);

                AppEvent::fire(new Created($subscription));
            } catch (\Throwable $e) {
                report($e);

                $request->session()->flash('error', 'Failed to make a subscription. Please, continue your account activation.');

                return redirect()->route('signup.owner-marine-contractor-account.payment-info', ['id' => $user->id])->withInput();
            }
        }

        return redirect()->route('activate-member-success');
    }
}
