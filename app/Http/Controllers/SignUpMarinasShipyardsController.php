<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\Geocoding;
use App\Http\Requests\SignUp\SignUpMarinasShipyardsBusinessAccountRequest;
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
 * Class SignUpMarinasShipyardsController
 * @package App\Http\Controllers
 */
class SignUpMarinasShipyardsController extends Controller
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
    public function signupOwnerMarinasShipyardsAccount()
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        return view('signup.marinas-shipyards-sign-up.owner-marinas-shipyards-sign-up');
    }

    /**
     * @param SignUpOwnerAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function storeOwnerMarinasShipyardsAccount(SignUpOwnerAccountRequest $request)
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
            $role = Sentinel::findRoleBySlug('marinas_shipyards');
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

            return redirect()->route('signup.owner-marinas-shipyards-account')->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect()->route('signup.owner-marinas-shipyards-account.business-info', ['id' => session()->get('signup-id')]);
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

            return view('signup.marinas-shipyards-sign-up.owner-marinas-shipyards-business-info', compact('id', 'serviceGroups', 'countries'));
        }

        return redirect()->route('activate-member-success');
    }

    /**
     * @param int $id
     * @param SignUpMarinasShipyardsBusinessAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBusinessAccount(int $id, SignUpMarinasShipyardsBusinessAccountRequest $request)
    {
        if ($id && session()->get('signup-id') === $id) {
            DB::beginTransaction();

            $files = [];
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

                    if ($request->hasfile('business.' . $key . '.map_file')) {
                        $storePath = 'maps';
                        $file = $request->file('business.' . $key . '.map_file');
                        $fmap = new File();

                        $fmap->mime = $file->getMimeType();
                        $fmap->size = $file->getSize();
                        $fmap->filename = $file->getClientOriginalName();
                        $fmap->disk = 'public';
                        $fmap->path = $file->store($storePath, ['disk' => $fmap->disk]);
                        $fmap->saveOrFail();
                        $files[] = $fmap;

                        $model->map_file_id = $fmap->id;
                        $model->saveOrFail();
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

                if ($files) {
                    foreach($files as $file) {
                        // Delete file in case if failed to update database
                        $file->cleanup();
                    }
                }

                $request->session()->flash('error', trans('auth/message.signup.error'));

                return redirect()->route('signup.owner-marinas-shipyards-account.business-info', ['id' => session()->get('signup-id')])->withInput();
            }
        }

        return redirect()->route('activate-member-success');
    }
}
