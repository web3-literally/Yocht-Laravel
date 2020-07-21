<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\Country;
use App\Helpers\Geocoding;
use App\Events\Member\Subscription\Created;
use App\Helpers\Place;
use App\Http\Requests\SignUp\SignUpOwnerAccountRequest;
use App\Http\Requests\SignUp\SignUpOwnerAccountPaymentInfoRequest;
use App\Http\Requests\SignUp\SignUpVesselAccountRequest;
use App\Mail\SignUp\MemberActivation;
use App\Models\Vessels\Vessel;
use App\Plan;
use App\Profile;
use App\Role;
use App\Subscription;
use App\User;
use Igaster\LaravelCities\Geo;
use Event as AppEvent;
use Sentinel;
use Mail;
use DB;

/**
 * Class SignUpYachtOwnerController
 * @package App\Http\Controllers
 */
class SignUpYachtOwnerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signupOwnerVesselAccount()
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        $action = route('signup.owner-vessel-account-store');

        return view('signup.owner-account-sign-up.owner-vessel-account-sign-up', compact('action'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signupOwnerTransferAccount()
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        $action = route('signup.owner-transfer-account-store');


        return view('signup.owner-account-sign-up.owner-transfer-account-sign-up', compact('action'));
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return bool
     * @throws \Throwable
     */
    protected function storeOwnerAccount(SignUpOwnerAccountRequest $request)
    {
        DB::beginTransaction();

        try {
            // Register a yacht owner account
            $data = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'country' => '',
                'user_state' => '',
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

                    $place = new Place($place);
                    $data['user_state'] = $place->getState();
                    $data['country'] = $place->getCountry();
                }
            }

            /** @var User $user */
            $user = Sentinel::register($data, false);

            // Add role for user
            /** @var Role $role */
            $role = Sentinel::findRoleBySlug('owner');
            $role->users()->attach($user);

            // Create as Customer
            $customer = $user->createAsBraintreeCustomer();
            $user->saveOrFail();

            // Make a profile
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->fill($request->get('profile', []));
            $profile->saveOrFail();

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

            return false;
        }

        return true;
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function storeOwnerVesselAccount(SignUpOwnerAccountRequest $request)
    {
        if (!$this->storeOwnerAccount($request)) {
            return redirect()->route('signup.owner-vessel-account')->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect()->route('signup.owner-account.vessel-info', ['id' => session()->get('signup-id')]);
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function storeOwnerTransferAccount(SignUpOwnerAccountRequest $request)
    {
        if (!$this->storeOwnerAccount($request)) {
            return redirect()->route('signup.owner-transfer-account')->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function signupVesselAccount(int $id)
    {
        if ($id && session()->get('signup-id') === $id) {
            /** @var User $owner */
            $owner = User::findOrFail($id);
            if ($owner->hasVessel()) {
                return redirect()->route('signup.owner-account.payment-info', ['id' => session()->get('signup-id')]);
            }

            resolve('seotools')->setTitle(trans('general.sign_up'));

            $fuelType = Vessel::getFuelType();

            $propulsion = config('propulsion');

            $countries = Country::getAll();

            $hullTypes = Vessel::getHullTypes();

            $vesselTypes = config('vessel-types');

            return view('signup.owner-account-sign-up.owner-account-vessel-info', compact('id', 'fuelType', 'propulsion', 'countries', 'hullTypes', 'vesselTypes'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @param SignUpVesselAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVesselAccount(int $id, SignUpVesselAccountRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            DB::beginTransaction();

            try {
                /** @var User $owner */
                $owner = User::findOrFail($id);

                // Register a vessel account
                /** @var User $user */
                $user = Sentinel::register([
                    'parent_id' => $owner->id,
                    'email' => uniqid('vessel_') . '@' . $_SERVER['SERVER_NAME'],
                    'password' => SignUpController::generateRandomString()
                ], false);
                $role = Sentinel::findRoleBySlug('vessel');
                $role->users()->attach($user);

                // Make a profile
                $model = new Vessel();
                $model->fill($request->except([]));
                $model->user_id = $user->id;
                $model->owner_id = $owner->id;
                $model->type = 'vessel';
                $model->charter = $request->get('charter', 0);
                $model->private = $request->get('private', 0);
                $model->is_primary = true;
                $model->owners = $request->input('owners');
                $model->staff = $request->input('captains');

                $model->saveOrFail();

                // Attach profile image
                if ($request->hasFile('file')) {
                    $storePath = 'vessels/images/' . $model->id;
                    $file = $request->file('file');
                    try {
                        $fl = new File();

                        $fl->mime = $file->getMimeType();
                        $fl->size = $file->getSize();
                        $fl->filename = $file->getClientOriginalName();
                        $fl->disk = 'public';
                        $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                        $fl->saveOrFail();

                        $model->attachImageFile($fl);

                        unset($fl);
                    } catch (\Throwable $e) {
                        report($e);

                        $request->session()->flash('error', 'Failed to process image file.');
                    } finally {
                        if (isset($fl->id) && $fl->id) {
                            // Delete file in case if failed to update database
                            $fl->delete();
                        }
                    }
                }

                $user->addToIndex();

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();

                report($e);

                $request->session()->flash('error', trans('auth/message.signup.error'));

                return redirect()->route('signup.owner-account.vessel-info', ['id' => session()->get('signup-id')])->withInput();
            }
        }

        return redirect()->route('signup.owner-account.payment-info', ['id' => session()->get('signup-id')]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function paymentInfo(int $id)
    {
        if ($id && session()->get('signup-id') === $id) {
            resolve('seotools')->setTitle(trans('general.sign_up'));

            $plans = Plan::active()->where('slug', 'like', "%owner%")->orderBy('billing_frequency', 'desc')->get();

            return view('signup.owner-account-sign-up.owner-account-payment-info', compact('id', 'plans'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @param SignUpOwnerAccountPaymentInfoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentInfoStore(int $id, SignUpOwnerAccountPaymentInfoRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            try {
                $user = User::findOrFail($id);

                // get the plan after submitting the form
                $plan = Plan::active()->where('slug', 'like', "%owner%")->findOrFail($request->plan);

                if (!$plan->active) {
                    throw new \Exception('Plan is not active', 500);
                }

                // subscribe the user
                $subscription = $user->newSubscription('Membership', $plan->braintree_plan)->create($request->payment_method_nonce, [
                    'email' => $user->email,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name
                ]);

                $subscription = Subscription::findOrFail($subscription->id);

                AppEvent::fire(new Created($subscription));

                session()->forget('signup-id');
            } catch (\Throwable $e) {
                report($e);

                $request->session()->flash('error', 'Failed to make a subscription. Please, continue your account activation.');

                return redirect()->route('signup.owner-account.payment-info', ['id' => session()->get('signup-id')])->withInput();
            }
        }

        return redirect()->route('activate-member-success');
    }
}
