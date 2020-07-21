<?php

namespace App\Http\Controllers;

use App\Helpers\Geocoding;
use App\Http\Requests\SignUp\SignUpLandServicesBusinessAccountRequest;
use App\Http\Requests\SignUp\SignUpOwnerAccountRequest;
use App\Mail\SignUp\MemberActivation;
use App\Models\Business\Business;
use App\Models\Services\ServiceGroup;
use App\Profile;
use App\Repositories\ServiceRepository;
use App\Role;
use App\User;
use Igaster\LaravelCities\Geo;
use Illuminate\Support\MessageBag;
use Sentinel;
use Mail;
use DB;

/**
 * Class SignUpLandServicesController
 * @package App\Http\Controllers
 */
class SignUpLandServicesController extends Controller
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
    public function signupOwnerLandServicesAccount()
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        return view('signup.land-services-sign-up.owner-land-services-sign-up');
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function storeOwnerLandServicesAccount(SignUpOwnerAccountRequest $request)
    {
        DB::beginTransaction();

        try {
            // Register a marinas shipyards account
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
            $role = Sentinel::findRoleBySlug('land_services');
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

            return redirect()->route('signup.owner-land-services-account')->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect()->route('signup.owner-land-services-account.business-info', ['id' => session()->get('signup-id')]);
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
                return redirect()->route('activate-member-success');
            }

            resolve('seotools')->setTitle(trans('general.sign_up'));

            $serviceGroups = ServiceGroup::providedBy($owner->getAccountType())->pluck('label', 'id');
            $countries = Geo::getCountries()->pluck('name', 'country')->all();

            return view('signup.land-services-sign-up.owner-land-services-business-info', compact('id', 'serviceGroups', 'countries'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @param SignUpLandServicesBusinessAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBusinessAccount(int $id, SignUpLandServicesBusinessAccountRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            DB::beginTransaction();

            try {
                /** @var User $owner */
                $owner = User::findOrFail($id);

                $role = Sentinel::findRoleBySlug('business');

                $i = 1;
                foreach($request->get('business') as $key => $business) {
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

                return redirect()->route('signup.owner-land-services-account.business-info', ['id' => session()->get('signup-id')])->withInput();
            }
        }

        return redirect()->route('activate-member-success');
    }
}
