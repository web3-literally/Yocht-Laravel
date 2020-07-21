<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentException;
use App\Helpers\Country;
use App\Mail\Manufacturers\ApproveBoatManufacturer;
use App\Models\Classifieds\ClassifiedsManufacturer;
use App\Profile;
use App\Repositories\ExtraVesselOfferRepository;
use Illuminate\Support\MessageBag;
use App\Helpers\Owner;
use App\Http\Requests\Vessels\TenderRequest;
use App\Models\Vessels\Vessel;
use Illuminate\Http\Request;
use App\File;
use DB;
use Mail;
use Sentinel;

/**
 * Class TendersController
 * @package App\Http\Controllers
 */
class TendersController extends Controller
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
     * @return array
     */
    protected function myVesselsDropdown()
    {
        $owner = Owner::currentOwner();
        $vessels = Vessel::where('owner_id', $owner->getUserId())
            ->whereNull('parent_id')
            ->where('type', 'vessel')
            ->orderBy('is_primary', 'desc')
            ->get()
            ->pluck('name', 'id')
            ->all();
        return $vessels;
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

        $countries = Country::getAll();

        $vessels = $this->myVesselsDropdown();

        return view('tenders.create', compact('vessels', 'fuelType', 'countries'));
    }

    /**
     * @param int $related_member_id
     * @param TenderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store($related_member_id, TenderRequest $request)
    {
        //$tenderCount = $this->extraOfferRepository->getTenderCount();

        DB::beginTransaction();

        try {
            $user = Sentinel::register([
                'parent_id' => Sentinel::getUser()->getUserId(),
                'email' => uniqid('tender_') . '@' . $_SERVER['SERVER_NAME'],
                'password' => SignUpController::generateRandomString()
            ], false);
            $role = Sentinel::findRoleBySlug('tender');
            $role->users()->attach($user);
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->fill([]);
            $profile->saveOrFail();

            $model = new Vessel();
            $model->fill($request->except('images', 'records', 'documents'));
            $model->parent_id = $request->get('parent_id');
            $model->user_id = $user->id;
            $model->owner_id = Owner::currentOwner()->getUserId();
            $model->type = 'tender';

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

            $result = $model->saveOrFail();

            if ($result) {
                $this->processImages($model, $request);
            }

            // Charge or Prolong extra tender offer if needed
            /*try {
                $custom = [
                    'for_boat_id' => $model->id
                ];
                if ($tenderCount >= $this->extraOfferRepository->getTenderSlotsCount()) {
                    // Charge for extra tender slot
                    $this->extraOfferRepository->chargeForExtraTender($custom);
                } else {
                    if ($tenderCount >= config('billing.vessel.free_tenders_count')) {
                        // Has paused offers, so prolong extra tender slot
                        $this->extraOfferRepository->prolongExtraTender($custom);
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

        /*if ($request->hasfile('records')) {
            $storePath = 'vessels/attachments';
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
            $storePath = 'vessels/attachments';
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

        if (!$result) {
            return redirect(route('account.vessels.add'))->withInput()->with('error', 'Failed to add tender.');
        }

        return redirect(route('account.vessels'))->with('success', 'Tender was successfully added.');
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

        $fuelType = Vessel::getFuelType();

        $countries = Country::getAll();

        $vessels = $this->myVesselsDropdown();

        return view('tenders.edit', compact('vessel', 'vessels', 'fuelType', 'countries'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param TenderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($related_member_id, $id, TenderRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = $this->loadVessel($id);

            $model->fill($request->except('images', 'records', 'documents'));
            $model->parent_id = $request->get('parent_id');

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

            $result = $model->save();

            if ($result) {
                $this->processImages($model, $request);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            $result = false;
        }

        /*if ($request->hasfile('records')) {
            $storePath = 'vessels/attachments';
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
            $storePath = 'vessels/attachments';
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

        if (!$result) {
            return redirect(route('account.tenders.edit', $model->id))->withInput()->with('error', 'Failed to update vessel.');
        }

        return redirect(route('account.vessels'))->with('success', 'Vessel was successfully updated.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove($related_member_id, $id)
    {
        $vessel = Vessel::my()->where('type', 'tender')->find($id);
        if (!$vessel) {
            throw new \Exception('Not found', 404);
        }

        $title = $vessel->title;

        DB::beginTransaction();

        try {
            $vessel->delete();

            //$this->extraOfferRepository->pauseExtraTender();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->route('account.vessels')->with('error', trans("Failed to delete {$title} tender."));
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
     * @param $id
     * @return Vessel
     * @throws \Exception
     */
    protected function loadVessel($id)
    {
        $owner = Owner::currentOwner();

        $vessel = Vessel::where('owner_id', $owner->getUserId())->where('type', 'tender')->find($id);
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
}
