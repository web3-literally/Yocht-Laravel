<?php

namespace App\Http\Controllers;

use App\Facades\GeoLocation;
use App\Helpers\Geocoding;
use App\Http\Requests\Businesses\BusinessAboutRequest;
use App\Http\Requests\Businesses\BusinessDetailsRequest;
use App\Http\Requests\Businesses\BusinessListingRequest;
use App\Http\Requests\Businesses\BusinessPhotoRequest;
use App\Http\Requests\Businesses\BusinessServicesRequest;
use App\Http\Requests\SignUp\SignUpLandServicesBusinessAccountRequest;
use App\Http\Requests\SignUp\SignUpMarinasShipyardsBusinessAccountRequest;
use App\Http\Requests\SignUp\SignUpMarineContractorBusinessAccountRequest;
use App\Jobs\Index\MembersUpdate;
use App\Models\Business\Business;
use App\Models\ServiceArea;
use App\Models\Services\ServiceGroup;
use App\User;
use Igaster\LaravelCities\Geo;
use Illuminate\Support\MessageBag;
use App\Helpers\Owner;
use App\Models\Vessels\Vessel;
use Illuminate\Http\Request;
use Event as AppEvent;
use App\File;
use Illuminate\Validation\Rule;
use Mail;
use Sentinel;
use DB;
use Validator;

/**
 * Class BusinessesController
 * @package App\Http\Controllers
 */
class BusinessesController extends Controller
{
    /**
     * BusinessController constructor.
     * @param MessageBag $messageBag
     */
    public function __construct(MessageBag $messageBag)
    {
        parent::__construct($messageBag);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function my()
    {
        $owner = Owner::currentOwner();

        if (!$owner->hasBusiness()) {
            return redirect(route('account.vessels.add'));
        }

        $businessTable = (new Business())->getTable();

        $businesses = Business::where('owner_id', $owner->getUserId())
            ->orderBy('is_primary', 'desc')
            ->orderBy($businessTable . '.company_name', 'asc')
            ->groupBy($businessTable . '.id')
            ->select($businessTable . '.*')
            ->paginate(10);

        return view('businesses.index', compact('businesses'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $type = $this->getAccountType();
        $serviceGroups = ServiceGroup::providedBy($type)->pluck('label', 'id');
        switch ($type) {
            case 'marinas_shipyards':
                $countries = Geo::getCountries()->pluck('name', 'country')->all();
                return view('businesses.signup.marinas-shipyards', compact('serviceGroups', 'countries'));
                break;
            case 'land_services':
                $countries = Geo::getCountries()->pluck('name', 'country')->all();
                return view('businesses.signup.land-services', compact('serviceGroups', 'countries'));
                break;
            default:
                return view('businesses.signup.marine-contractor', compact('serviceGroups'));
        }
    }

    public function store(Request $request)
    {
        $type = $this->getAccountType();
        switch ($type) {
            case 'marinas_shipyards':
                /** @var SignUpMarinasShipyardsBusinessAccountRequest $formRequest */
                $formRequest = resolve(SignUpMarinasShipyardsBusinessAccountRequest::class);
                break;
            case 'land_services':
                /** @var SignUpLandServicesBusinessAccountRequest $formRequest */
                $formRequest = resolve(SignUpLandServicesBusinessAccountRequest::class);
                break;
            default:
                /** @var SignUpMarineContractorBusinessAccountRequest $formRequest */
                $formRequest = resolve(SignUpMarineContractorBusinessAccountRequest::class);
        }

        DB::beginTransaction();

        $files = [];
        try {
            /** @var User $owner */
            $owner = Owner::currentOwner();

            $role = Sentinel::findRoleBySlug('business');

            foreach ($request->get('business') as $key => $business) {
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
                $model->is_primary = !Business::where('owner_id', $owner->getUserId())->exists();
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

                if ($model->business_type == 'marinas_shipyards' &&  $request->hasfile('business.' . $key . '.map_file')) {
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
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if ($files) {
                foreach ($files as $file) {
                    // Delete file in case if failed to update database
                    $file->cleanup();
                }
            }

            $request->session()->flash('error', trans('auth/message.signup.error'));

            return redirect()->route('account.businesses.add')->withInput()->with('error', 'Failed to add business.');
        }

        return redirect()->route('account.businesses')->with('success', 'Business was successfully added.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit($id)
    {
        $business = $this->loadBusiness($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        return view('businesses.edit', compact('business', 'countries'));
    }

    /**
     * @param int $id
     * @param BusinessDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($id, BusinessDetailsRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadBusiness($id);

            $fields = $model->getFillable();
            $model->fill($request->only(array_diff($fields, ['staff', 'owners', 'categories', 'services'])));
            if ($request->get('is_primary')) {
                $model->is_primary = true;
            }

            if ($request->get('is_primary')) {
                Business::my()->where('id', '!=', $model->id)->update(['is_primary' => 0]);
            }

            $address = trim("{$model->company_address} {$model->company_city}");
            $response = Geocoding::latlngLookup($address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = current($response->results);
                    $model->map_lat = $place->geometry->location->lat;
                    $model->map_lng = $place->geometry->location->lng;
                }
            }

            if ($model->business_type == 'marinas_shipyards' && $request->hasfile('map_file')) {
                $oldMapFileId = $model->map_file_id;
                $storePath = 'maps';
                $file = $request->file('map_file');
                $fmap = new \App\File();

                $fmap->mime = $file->getMimeType();
                $fmap->size = $file->getSize();
                $fmap->filename = $file->getClientOriginalName();
                $fmap->disk = 'public';
                $fmap->path = $file->store($storePath, ['disk' => $fmap->disk]);
                if ($fmap->save()) {
                    $model->map_file_id = $fmap->id;
                } else {
                    $request->session()->flash('error', 'Failed to process map file.');
                }

                if ($model->save()) {
                    $fl = \App\File::find($oldMapFileId);
                    $fl->delete();
                }
            }

            $model->saveOrFail();

            //$this->processImages($model, $request);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return back()->withInput()->with('error', 'Failed to update business.');
        }

        return back()->with('success', 'Business was successfully updated.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function listing($id)
    {
        $business = $this->loadBusiness($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);
        $countries = Geo::getCountries()->pluck('name', 'country')->all();

        return view('businesses.listing', compact('business', 'countries'));
    }

    /**
     * @param int $id
     * @param BusinessListingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function listingUpdate($id, BusinessListingRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadBusiness($id);

            $fields = $model->getFillable();
            $model->fill($request->only(array_diff($fields, ['staff', 'owners', 'categories', 'services'])));

            $model->staff = $request->get('staff');
            $model->owners = $request->get('owners');

            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return back()->withInput()->with('error', 'Failed to update business.');
        }

        if ($request->hasfile('brochure_file')) {
            $oldBrochureFileId = $model->brochure_file_id;
            $storePath = 'brochures';
            $file = $request->file('brochure_file');
            $fmap = new \App\File();

            $fmap->mime = $file->getMimeType();
            $fmap->size = $file->getSize();
            $fmap->filename = $file->getClientOriginalName();
            $fmap->disk = 'public';
            $fmap->path = $file->store($storePath, ['disk' => $fmap->disk]);
            if ($fmap->save()) {
                $model->brochure_file_id = $fmap->id;
            } else {
                $request->session()->flash('error', 'Failed to process map file.');
            }

            if ($model->save()) {
                if ($fl = \App\File::find($oldBrochureFileId)) {
                    $fl->delete();
                }
            }
        }

        return back()->with('success', 'Business was successfully updated.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function photo($id)
    {
        $business = $this->loadBusiness($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        return view('businesses.photo', compact('business'));
    }

    /**
     * @param int $id
     * @param BusinessPhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function photoUpdate($id, BusinessPhotoRequest $request)
    {
        $model = $this->loadBusiness($id);

        $this->processImages($model, $request);

        MembersUpdate::dispatch($model->user)
            ->onQueue('high');

        return back()->with('success', 'Business was successfully updated.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function video($id)
    {
        $business = $this->loadBusiness($id);
        if (!$business->user->hasAccess(['profile.video'])) {
            abort(404);
        }

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        return view('businesses.video', compact('business'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function videoStore($id, Request $request)
    {
        $model = $this->loadBusiness($id);
        if (!$model->user->hasAccess(['profile.video'])) {
            abort(404);
        }

        $result = [];

        if ($request->hasfile('file')) {
            $file = $request->file('file');
            if ($model->attachments()->where('type', 'video')->exists()) {
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => 'The video file already uploaded.'
                ];

                return response()->json(['file' => $result]);
            }

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'file', 'mimes:mp4', 'max:100000']
            ]);

            if ($validator->fails()) {
                $bag = $validator->getMessageBag();
                $message = $bag->first('file');
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $message
                ];

                return response()->json(['file' => $result]);
            }

            $storePath = 'businesses/videos/' . $model->id;
            try {
                $fl = new File();

                $fl->mime = $file->getMimeType();
                $fl->size = $file->getSize();
                $fl->filename = $file->getClientOriginalName();
                $fl->disk = 'video';
                $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                $fl->saveOrFail();

                $model->attachFile($fl, 'video');

                unset($fl);

                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ];
            } catch (\Throwable $e) {
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $e->getMessage()
                ];

                report($e);
            } finally {
                if (isset($fl->id) && $fl->id) {
                    // Delete file in case if failed to update database
                    $fl->delete();
                }
            }
        }

        return response()->json(['file' => $result]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function videoDelete($id)
    {
        $model = $this->loadBusiness($id);
        if (!$model->user->hasAccess(['profile.video'])) {
            abort(404);
        }

        $link = $model->attachments()->where('type', 'video')->first();
        if (!$link) {
            abort(404);
        }

        if ($link->delete()) {
            return redirect(route('account.businesses.profile.video', ['business_id' => $id]))->with('success', 'Video was successfully deleted.');
        }

        return redirect(route('account.businesses.profile.video', ['business_id' => $id]))->with('error', 'Failed to delete video file.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function about($id)
    {
        $business = $this->loadBusiness($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        return view('businesses.about', compact('business'));
    }

    /**
     * @param int $id
     * @param BusinessAboutRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function aboutUpdate($id, BusinessAboutRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadBusiness($id);

            $model->fill($request->only('description'));

            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return back()->withInput()->with('error', 'Failed to update business.');
        }

        return back()->with('success', 'Business was successfully updated.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function services($id)
    {
        $business = $this->loadBusiness($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        $serviceGroups = ServiceGroup::providedBy($business->owner->getAccountType())->pluck('label', 'id');

        return view('businesses.services', compact('business', 'serviceGroups'));
    }

    /**
     * @param $id
     * @param BusinessServicesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function servicesUpdate($id, BusinessServicesRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadBusiness($id);

            $model->user->categories()->sync($request->get('categories'));
            $model->user->services()->sync($request->get('services'));

            MembersUpdate::dispatch($model->user)
                ->onQueue('high');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return back()->withInput()->with('error', 'Failed to update business.');
        }

        return back()->with('success', 'Business was successfully updated.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function serviceAreas($id)
    {
        $business = $this->loadBusiness($id);

        $user = $business->user;
        if (!$user->hasAccess(['profile.service-areas'])) {
            abort(404);
        }

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $business->name);

        $serviceAreas = $user->service_areas;

        return view('businesses.service-areas', compact('business', 'serviceAreas'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function serviceAreasUpdate($id, Request $request)
    {
        $business = $this->loadBusiness($id);

        $member = $business->user;
        if (!$member->hasAccess(['profile.service-areas'])) {
            abort(404);
        }

        $request->validate([
            'action' => ['required', Rule::in(['add', 'delete'])],
            'location' => ['nullable', 'required_if:action,add'],
            'location_type' => ['nullable', 'required_if:action,add', Rule::in(['city', 'county', 'state', 'country'])],
            'location_country' => ['nullable', 'required_if:action,add'],
            'id' => ['nullable', 'required_if:action,delete', 'numeric'],
        ]);

        if ($request->get('action') == 'add') {
            $searchMethod = 'search'.ucfirst($request->get('location_type'));
            $locationData = GeoLocation::$searchMethod($request->get('location'), $request->get('location_country'));
            if (empty($locationData)) {
                abort(404);
            }

            $locationData = current($locationData);

            $area = new ServiceArea();
            $area->fill(['location_id' => $locationData->geonameId]);
            $area->user_id = $member->getUserId();
            $area->saveOrFail();

            MembersUpdate::dispatch($member)
                ->onQueue('high');

            return response()->json(true);
        }

        if ($request->get('action') == 'delete') {
            $area = ServiceArea::where('user_id', $member->getUserId())->where('id', $request->get('id'))->first();
            if (!$area) {
                abort(404);
            }
            $area->delete();

            MembersUpdate::dispatch($member)
                ->onQueue('high');

            return response()->json(true);
        }

        abort(404);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    /*public function remove($id)
    {
        $vessel = Vessel::my()->where('type', 'vessel')->find($id);
        if (!$vessel) {
            throw new \Exception('Not found', 404);
        }

        if ($vessel->is_primary) {
            if (Vessel::my()->count() == 1) {
                // If only primary left we allow to delete vessel
            } else {
                return redirect(route('account.vessels'))->with('error', "You can't delete primary vessel.");
            }
        }

        $title = $vessel->title;

        DB::beginTransaction();

        try {
            $vessel->delete();

            //$this->extraOfferRepository->pauseExtraVessel();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->route('account.vessels')->with('error', trans("Failed to delete {$title} vessel."));
        }

        return redirect(route('account.vessels'))->with('success', "{$title} was successfully deleted.");
    }*/

    /**
     * @param int $id
     * @param int $image_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteImage($id, $image_id, Request $request)
    {
        $business = $this->loadBusiness($id);

        $success = false;
        if ($business->images->count()) {
            foreach ($business->images as $image) {
                if ($image->id == $image_id) {
                    $success = $image->delete();
                    break;
                }
            }
        }

        return response()->json(['success' => $success]);
    }

    /**
     * @param int $id
     * @return Vessel
     * @throws \Exception
     */
    protected function loadBusiness($id)
    {
        $owner = Owner::currentOwner();

        $builder = Business::where('owner_id', $owner->getUserId());
        $business = $builder->find($id);
        if (!$business) {
            throw new \Exception('Not found', 404);
        }

        return $business;
    }

    /**
     * @return string
     */
    protected function getAccountType()
    {
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->parent_id) {
            $user = $user->parent;
        }

        return $user->getAccountType();
    }

    /**
     * @param Vessel $model
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    protected function processImages(Business $model, Request $request)
    {
        $result = true;

        if ($request->hasfile('images')) {
            $storePath = 'business/images/' . $model->id;
            foreach ($request->file('images') as $i => $file) {
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
                    $request->session()->flash('error', 'Failed to process image.' . $i . ' file.');

                    $result = false;
                } finally {
                    if (isset($fl->id) && $fl->id) {
                        // Delete file in case if failed to update database
                        $fl->delete();
                    }
                }
            }
        }

        return $result;
    }
}
