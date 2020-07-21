<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentException;
use App\Facades\GeoLocation;
use App\Helpers\Country;
use App\Http\Requests\Vessels\AboutRequest;
use App\Mail\Manufacturers\ApproveBoatManufacturer;
use App\Models\Classifieds\ClassifiedsManufacturer;
use App\Repositories\ExtraVesselOfferRepository;
use Illuminate\Support\MessageBag;
use App\Events\Vessel\Relocate;
use App\Helpers\Owner;
use App\Http\Requests\Vessels\VesselLocationRequest;
use App\Http\Requests\Vessels\VesselSignupRequest;
use App\Http\Requests\Vessels\VesselUpdateRequest;
use App\Models\Vessels\Vessel;
use App\Profile;
use Illuminate\Http\Request;
use Event as AppEvent;
use App\File;
use Mail;
use Validator;
use Sentinel;
use DB;

/**
 * Class VesselsController
 * @package App\Http\Controllers
 */
class VesselsController extends Controller
{
    /**
     * @var ExtraVesselOfferRepository
     */
    protected $extraOfferRepository;

    /**
     * BoatCrewController constructor.
     * @param MessageBag $messageBag
     * @param ExtraVesselOfferRepository $extraOfferRepository
     */
    public function __construct(MessageBag $messageBag, ExtraVesselOfferRepository $extraOfferRepository)
    {
        parent::__construct($messageBag);

        $this->extraOfferRepository = $extraOfferRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function my()
    {
        $owner = Owner::currentOwner();

        if (!$owner->hasVessel()) {
            return redirect(route('account.vessels.add'));
        }

        $vesselTable = (new Vessel())->getTable();

        $vessels = Vessel::where('owner_id', $owner->getUserId())
            ->whereNull('parent_id')
            ->orderBy('is_primary', 'desc')
            ->orderBy('type', 'asc')
            ->orderBy($vesselTable . '.name', 'asc')
            ->groupBy($vesselTable . '.id')
            ->select($vesselTable . '.*')
            ->paginate(10);

        //$vesselCount = $this->extraOfferRepository->getVesselCount();
        //$vesselSlotsCount = $this->extraOfferRepository->getVesselSlotsCount();
        //$tenderCount = $this->extraOfferRepository->getTenderCount();
        //$tenderSlotsCount = $this->extraOfferRepository->getTenderSlotsCount();

        return view('vessels.index', compact('vessels'/*, 'vesselCount', 'vesselSlotsCount', 'tenderCount', 'tenderSlotsCount'*/));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturersData(Request $request)
    {
        $results = ClassifiedsManufacturer::where('type', 'boat')->where('title', 'like', '%' . $request->get('search') . '%')->orderBy('title')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->title
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($related_member_id, Request $request)
    {
        $fuelType = Vessel::getFuelType();

        $propulsion = config('propulsion');

        $countries = Country::getAll();

        $hullTypes = Vessel::getHullTypes();

        $vesselTypes = config('vessel-types');

        return view('vessels.create', compact('fuelType', 'propulsion', 'countries', 'hullTypes', 'vesselTypes'));
    }

    /**
     * @param int $related_member_id
     * @param VesselSignupRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store($related_member_id, VesselSignupRequest $request)
    {
        //$vesselCount = $this->extraOfferRepository->getVesselCount();

        DB::beginTransaction();

        try {
            $user = Sentinel::register([
                'parent_id' => Sentinel::getUser()->getUserId(),
                'email' => uniqid('vessel_') . '@' . $_SERVER['SERVER_NAME'],
                'password' => SignUpController::generateRandomString()
            ], false);
            $role = Sentinel::findRoleBySlug('vessel');
            $role->users()->attach($user);
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->fill([]);
            $profile->saveOrFail();

            $model = new Vessel();
            $model->fill($request->except('images', 'records', 'documents'));
            $model->user_id = $user->id;
            $model->owner_id = Owner::currentOwner()->getUserId();
            $model->type = 'vessel';
            $model->charter = $request->get('charter', 0);
            $model->private = $request->get('private', 0);
            $model->is_primary = !boolval(Vessel::my()->count());
            $model->owners = $request->input('owners');
            $model->staff = $request->input('captains');

            $manufacturer = $request->get('manufacturer_id');
            if (!is_numeric($manufacturer)) {
                $newManufacturer = new ClassifiedsManufacturer();
                $newManufacturer->by_id = Sentinel::getUser()->getUserId();
                $newManufacturer->title = ucfirst($manufacturer);
                $newManufacturer->type = 'boat';
                $newManufacturer->saveOrFail();

                $model->manufacturer_id = $newManufacturer->id;

                Mail::send(new ApproveBoatManufacturer($newManufacturer));
            }

            $registered_port_id = intval($request->get('registered_port_id'));
            if ($registered_port_id) {
                $model->registered_port = GeoLocation::getLabel($registered_port_id);
            }

            $result = $model->saveOrFail();

            // Charge or Prolong extra vessel offer if needed
            /*try {
                $custom = [
                    'for_boat_id' => $model->id
                ];
                if ($vesselCount >= $this->extraOfferRepository->getVesselSlotsCount()) {
                    // Charge for extra vessel slot
                    $this->extraOfferRepository->chargeForExtraVessel($custom);
                } else {
                    if ($vesselCount >= config('billing.vessel.free_vessels_count')) {
                        // Has paused offers, so prolong extra vessel slot
                        $this->extraOfferRepository->prolongExtraVessel($custom);
                    }
                }
                // Other cases, has free vessel slots
            } catch (\Exception $e) {
                report($e);

                throw new PaymentException('Failed to charge additional fee. ' . $e->getMessage(), $e->getCode(), $e);
            }*/

            DB::commit();
        } catch (PaymentException $e) {
            DB::rollback();

            $result = false;
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            $result = false;
        }

        if ($result) {
            $this->processImages($model, $request);

            /*if ($request->hasfile('records')) {
                $storePath = 'vessels/attachments/' . $model->id;
                foreach ($request->file('records') as $i => $file) {
                    try {
                        $fl = new File();

                        $fl->mime = $file->getMimeType();
                        $fl->size = $file->getSize();
                        $fl->filename = $file->getClientOriginalName();
                        $fl->disk = 'public';
                        $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                        $fl->saveOrFail();

                        $model->attachFile($fl, 'record');

                        unset($fl);
                    } catch (\Throwable $e) {
                        $request->session()->flash('error', 'Failed to process record.' . $i . ' file.');
                    } finally {
                        if (isset($fl->id) && $fl->id) {
                            // Delete file in case if failed to update database
                            $fl->delete();
                        }
                    }
                }
            }

            if ($request->hasfile('documents')) {
                $storePath = 'vessels/attachments/' . $model->id;
                foreach ($request->file('documents') as $i => $file) {
                    try {
                        $fl = new File();

                        $fl->mime = $file->getMimeType();
                        $fl->size = $file->getSize();
                        $fl->filename = $file->getClientOriginalName();
                        $fl->disk = 'public';
                        $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                        $fl->saveOrFail();

                        $model->attachFile($fl, 'document');

                        unset($fl);
                    } catch (\Throwable $e) {
                        $request->session()->flash('error', 'Failed to process document.' . $i . ' file.');
                    } finally {
                        if (isset($fl->id) && $fl->id) {
                            // Delete file in case if failed to update database
                            $fl->delete();
                        }
                    }
                }
            }*/

            $user->addToIndex();
        }

        if (!$result) {
            return redirect(route('account.vessels.add'))->withInput()->with('error', 'Failed to add vessel.');
        }

        if ($model->is_primary) {
            return redirect(route('account.vessels', ['related_member_id' => $model->user_id]))->with('success', 'Primary vessel was successfully added.');
        }

        return redirect(route('account.vessels'))->with('success', 'Vessel was successfully added.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit($related_member_id, $id)
    {
        $vessel = $this->loadVessel($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $vessel->name);

        $fuelType = Vessel::getFuelType();

        $propulsion = config('propulsion');

        $countries = Country::getAll();

        $hullTypes = Vessel::getHullTypes();

        $vesselTypes = config('vessel-types');

        return view('vessels.edit', compact('vessel', 'fuelType', 'propulsion', 'countries', 'hullTypes', 'vesselTypes'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param VesselUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($related_member_id, $id, VesselUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadVessel($id);

            $model->fill($request->except('images', 'records', 'documents'));
            if ($request->get('is_primary')) {
                $model->is_primary = true;
            }
            $model->charter = $request->get('charter', 0);
            $model->private = $request->get('private', 0);

            $manufacturer = $request->get('manufacturer_id');
            if (!is_numeric($manufacturer)) {
                $newManufacturer = new ClassifiedsManufacturer();
                $newManufacturer->by_id = Sentinel::getUser()->getUserId();
                $newManufacturer->title = ucfirst($manufacturer);
                $newManufacturer->type = 'boat';
                $newManufacturer->saveOrFail();

                $model->manufacturer_id = $newManufacturer->id;

                Mail::send(new ApproveBoatManufacturer($newManufacturer));
            }

            $registered_port_id = intval($request->get('registered_port_id'));
            if ($registered_port_id && $model->getOriginal('registered_port_id') != $registered_port_id) {
                $model->registered_port = GeoLocation::getLabel($registered_port_id);
            }

            $result = $model->save();

            if ($result && $request->get('is_primary')) {
                Vessel::my()->where('id', '!=', $model->id)->update(['is_primary' => 0]);
            }

            if ($result) {
                $this->processImages($model, $request);
            }

            /*if ($request->hasfile('records')) {
                $storePath = 'vessels/attachments/' . $model->id;
                foreach ($request->file('records') as $i => $file) {
                    try {
                        $fl = new File();

                        $fl->mime = $file->getMimeType();
                        $fl->size = $file->getSize();
                        $fl->filename = $file->getClientOriginalName();
                        $fl->disk = 'public';
                        $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                        $fl->saveOrFail();

                        $model->attachFile($fl, 'record');

                        unset($fl);
                    } catch (\Throwable $e) {
                        $request->session()->flash('error', 'Failed to process record.' . $i . ' file.');
                    } finally {
                        if (isset($fl->id) && $fl->id) {
                            // Delete file in case if failed to update database
                            $fl->delete();
                        }
                    }
                }
            }*/

            /*if ($request->hasfile('documents')) {
                $storePath = 'vessels/attachments/' . $model->id;
                foreach ($request->file('documents') as $i => $file) {
                    try {
                        $fl = new File();

                        $fl->mime = $file->getMimeType();
                        $fl->size = $file->getSize();
                        $fl->filename = $file->getClientOriginalName();
                        $fl->disk = 'public';
                        $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                        $fl->saveOrFail();

                        $model->attachFile($fl, 'document');

                        unset($fl);
                    } catch (\Throwable $e) {
                        $request->session()->flash('error', 'Failed to process document.' . $i . ' file.');
                    } finally {
                        if (isset($fl->id) && $fl->id) {
                            // Delete file in case if failed to update database
                            $fl->delete();
                        }
                    }
                }
            }*/

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            $result = false;
        }

        if (!$result) {
            return redirect()->route('account.vessels.profile.details', $model->id)->withInput()->with('error', 'Failed to update vessel.');
        }

        return redirect()->route('account.vessels')->with('success', 'Vessel was successfully updated.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove($related_member_id, $id)
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

        if ($related_member_id == $vessel->user_id) {
            return redirect(route('account.vessels', ['related_member_id' => '-']))->with('success', "{$title} was successfully deleted.");
        }

        return redirect(route('account.vessels'))->with('success', "{$title} was successfully deleted.");
    }

    /**
     * @param int $related_member_id
     * @param int $vessel_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteImage($related_member_id, $vessel_id, $id, Request $request)
    {
        $vessel = $this->loadVessel($vessel_id);

        $success = false;
        if ($vessel->images->count()) {
            foreach ($vessel->images as $image) {
                if ($image->id == $id) {
                    $success = $image->delete();
                    break;
                }
            }
        }

        return response()->json(['success' => $success]);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function setVessel(Request $request)
    {
        $id = $request->get('id');
        $vessel = $this->loadVessel($id, true);

        $request->session()->put('current-vessel', $vessel->id);

        return back();
    }

    /**
     * @param VesselLocationRequest $request
     * @return string
     * @throws \Throwable
     */
    public function setLocation(VesselLocationRequest $request)
    {
        $id = $request->get('id');
        $boat = $this->loadVessel($id, true);

        DB::transaction(function () use ($boat, $request) {
            $boat->address = $request->get('address');
            $boat->location_city = $request->get('location_city');
            $boat->location_country = $request->get('location_country');
            $boat->map_lat = $request->get('lat');
            $boat->map_lng = $request->get('lng');

            $boat->saveOrFail();

            AppEvent::fire(new Relocate($boat));
        });

        return 'true';
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function video($related_member_id, $id)
    {
        $vessel = $this->loadVessel($id);
        if (!$vessel->user->hasAccess(['profile.video'])) {
            abort(404);
        }

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $vessel->name);

        return view('vessels.video', compact('vessel'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function videoStore($related_member_id, $id, Request $request)
    {
        $model = $this->loadVessel($id);
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

            $storePath = 'vessels/videos/' . $model->id;
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
     * @param int $related_member_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function videoDelete($related_member_id, $id)
    {
        $model = $this->loadVessel($id);
        if (!$model->user->hasAccess(['profile.video'])) {
            abort(404);
        }

        $link = $model->attachments()->where('type', 'video')->first();
        if (!$link) {
            abort(404);
        }

        if ($link->delete()) {
            return redirect(route('account.vessels.profile.video', ['boat_id' => $id]))->with('success', 'Video was successfully deleted.');
        }

        return redirect(route('account.vessels.profile.video', ['boat_id' => $id]))->with('error', 'Failed to delete video file.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function about($related_member_id, $id)
    {
        $vessel = $this->loadVessel($id);

        resolve('seotools')->setTitle(trans('general.manage_profile') . config('seotools.meta.defaults.separator') . $vessel->name);

        return view('vessels.about', compact('vessel'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param AboutRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function aboutUpdate($related_member_id, $id, AboutRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadVessel($id);

            $model->fill($request->only('description'));

            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return back()->withInput()->with('error', 'Failed to update vessel.');
        }

        return back()->with('success', 'Vessel was successfully updated.');
    }

    /**
     * @param int $id
     * @param bool $all
     * @return Vessel
     * @throws \Exception
     */
    protected function loadVessel($id, $all = false)
    {
        $owner = Owner::currentOwner();

        $builder = Vessel::where('owner_id', $owner->getUserId());
        if (!$all) {
            $builder->where('type', 'vessel');
        }
        $vessel = $builder->find($id);
        if (!$vessel) {
            throw new \Exception('Not found', 404);
        }

        return $vessel;
    }

    /**
     * @param Vessel $model
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    protected function processImages(Vessel $model, Request $request)
    {
        $result = true;

        if ($request->hasfile('images')) {
            $storePath = 'vessels/images/' . $model->id;
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function colorUpdate(Request $request)
    {
        $vessel = $this->loadVessel($request->get('vessel_id'));
        $color = strtolower($request->get('color'));
        if (!in_array($color, array_keys(\App\Helpers\Vessel::colors()))) {
            abort(400);
        }

        $vessel->listing_color = $color;
        $vessel->saveOrFail();

        return response()->json([]);
    }
}
