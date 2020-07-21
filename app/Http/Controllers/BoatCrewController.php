<?php

namespace App\Http\Controllers;

use App\CrewMember;
use App\Exceptions\PaymentException;
use App\Helpers\Country;
use App\Helpers\Crew;
use App\Helpers\Owner;
use App\Http\Requests\AssignMemberRequest;
use App\Http\Requests\CreateMemberRequest;
use App\Mail\Boats\MemberAssigned;
use App\Mail\Boats\MemberUnAssigned;
use App\Mail\SignUp\CrewMemberActivation;
use App\Models\Position;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselsCrew;
use App\Profile;
use App\Repositories\ExtraCrewOfferRepository;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Sentinel;
use Mail;
use DB;

/**
 * Class BoatCrewController
 * @package App\Http\Controllers
 */
class BoatCrewController extends Controller
{
    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @var ExtraCrewOfferRepository
     */
    protected $extraOfferRepository;

    /**
     * @param MessageBag $messageBag
     * @param ExtraCrewOfferRepository $extraOfferRepository
     */
    public function __construct(MessageBag $messageBag, ExtraCrewOfferRepository $extraOfferRepository)
    {
        parent::__construct($messageBag);

        $this->extraOfferRepository = $extraOfferRepository;
    }

    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index($related_member_id)
    {
        /** @var User $owner */
        $owner = Owner::currentOwner();
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);
        $vesselId = $vessel->id;

        resolve('seotools')->setTitle(trans('crew.crew') . config('seotools.meta.defaults.separator') . $vessel->name);

        $userTable = (new CrewMember())->getTable();

        $builder = $owner->crew()
            ->leftJoin('vessels_crew', $userTable . '.id', '=', 'vessels_crew.user_id')
                ->where(function($builder) use ($vesselId) {
                    $builder->whereNull('vessels_crew.vessel_id')->orWhere('vessels_crew.vessel_id', $vesselId);
                });
        if ($assigned = $vessel->crew()->orderBy('users.first_name', 'asc')->orderBy('users.last_name', 'asc')->pluck('user_id')->all()) {
            $builder->orderByRaw('FIELD(users.id, ' . implode(',', array_reverse($assigned)) . ') DESC');
        }
        $crew = $builder->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->paginate(10);

        $vesselTeamSlotsCount = $this->extraOfferRepository->getVesselTeamSlotsCount($vessel->id);

        return view('vessels.crew.index', compact('vessel', 'crew', 'assigned', 'vesselTeamSlotsCount'));
    }

    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function create($related_member_id)
    {
        resolve('seotools')->setTitle(trans('crew.create_member'));

        /** @var CrewMember $roles Hide crew accounts for now you will only select captain on release */
        $roles = Role::whereIn('slug', CrewMember::CREW_ROLES)->where('slug', '!=', 'crew')->pluck('name', 'slug')->all();
        $countries = Country::getOptions();
        $positions = Position::where('slug', '!=', 'captain')->pluck('label', 'id')->all();

        return view('vessels.crew.create', compact('roles', 'positions', 'countries'));
    }

    /**
     * @param int $related_member_id
     * @param CreateMemberRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store($related_member_id, CreateMemberRequest $request)
    {
        /** @var User $owner */
        $owner = Sentinel::getUser();

        // Register crew member
        DB::beginTransaction();

        $files = [];
        try {
            $data = $request->only(['email', 'first_name', 'last_name', 'phone', 'country', 'position_id']);
            $data['position_id'] = $request->get('role') == 'crew' ? $data['position_id'] : Position::where('slug', 'captain')->first()->id;
            $data['parent_id'] = $owner->isCaptainAccount() ? $owner->parent_id : $owner->id;
            $data['password'] = SignUpController::generateRandomString(); // Generate temporary password

            /** @var User $member */
            $member = Sentinel::register($data, false);

            // Add role for user
            /** @var Role $role */
            $role = Sentinel::findRoleBySlug($request->get('role'));
            $role->users()->attach($member);

            // Make a profile
            $memberProfile = new Profile();
            $memberProfile->user_id = $member->id;
            $memberProfile->fill($request->only(['experience']));
            $memberProfile->saveOrFail();

            if ($request->hasFile('pic')) {
                resolve(UserRepository::class)->attachProfileImage($member, $request->file('pic'));
            }

            if ($request->hasFile('cv')) {
                resolve(UserRepository::class)->attachCV($member, $request->file('cv'));
            }

            if ($request->hasfile('certificates')) {
                $storePath = 'profiles/certificates/' . $member->id;

                $certificates = $request->file('certificates');
                foreach ($certificates as $file) {
                    $fl = new \App\File();

                    $fl->mime = $file->getMimeType();
                    $fl->size = $file->getSize();
                    $fl->filename = $file->getClientOriginalName();
                    $fl->disk = 'local';
                    $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                    $fl->saveOrFail();

                    $member->profile->attachFile($fl, 'certificate');
                    $files[] = $fl;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            if ($files) {
                foreach($files as $fl) {
                    $fl->delete();
                }
            }

            return redirect()->back()->withInput()->with('error', trans('crew.failed_to_create') . ' ' . $e->getMessage());
        }

        /**
         * Sending welcome email
         */
        if (empty($userId)) {
            $mail = new CrewMemberActivation($member);
            Mail::send($mail);
        }

        return redirect()->route('account.boat.crew.index')->with('success', trans('crew.member_was_created', [
            'member' => $member->full_name
        ]));
    }

    /**
     * @param int $boat_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
//    public function assign(int $boat_id)
//    {
//        /** @var Vessel $vessel */
//        $vessel = $this->loadVessel($boat_id);
//
//        resolve('seotools')->setTitle(trans('crew.assign') . config('seotools.meta.defaults.separator') . $vessel->name);
//
//        $except = VesselsCrew::where('owner_id', $vessel->owner->id)->pluck('user_id')->all();
//        $query = CrewMember::crewAccounts();
//        if (Sentinel::getUser()->isCaptainAccount()) {
//            // Captain can't assign a captain
//            $roleTable = (new Role())->getTable();
//            $query->where($roleTable . '.slug', '!=', 'captain');
//        }
//        $submembers = $query
//            ->where('parent_id', $vessel->owner->id)
//            ->whereNotIn('users.id', $except)
//            ->orderBy('first_name', 'asc')
//            ->orderBy('last_name', 'asc')
//            ->get()->mapWithKeys(function ($item) {
//                return [$item->id => "{$item->full_name} ({$item->position_label})"];
//            })->all();
//
//        $roles = Role::whereIn('slug', CrewMember::CREW_ROLES)->pluck('name', 'slug')->all();
//
//        $positions = Position::where('slug', '!=', 'captain')->pluck('label', 'id')->all();
//
//        if (Sentinel::getUser()->isCaptainAccount()) {
//            // Captain can't assign a captain
//            if (($key = array_search('captain', $roles)) !== false) {
//                unset($roles[$key]);
//            }
//            $roles = ['crew'];
//        }
//
//        return view('vessels.crew.assign', compact('vessel', 'submembers', 'roles', 'positions'));
//    }

    /**
     * @param int $boat_id
     * @param AssignMemberRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
//    public function assignMember(int $boat_id, AssignMemberRequest $request)
//    {
//        /** @var User $owner */
//        $owner = Sentinel::getUser();
//
//        /** @var Vessel $vessel */
//        $vessel = $this->loadVessel($boat_id);
//
//        /** @var int $crewmembersCount */
//        $crewmembersCount = $vessel->crew->count();
//
//        // Register crew member
//        DB::beginTransaction();
//
//        try {
//            if ($userId = $request->get('user_id')) {
//                $member = $this->loadMember($userId);
//            } else {
//                $data = $request->only(['email', 'first_name', 'last_name', 'phone', 'position_id']);
//                $data['position_id'] = $request->get('role') == 'crew' ? $data['position_id'] : Position::where('slug', 'captain')->first()->id;
//                $data['parent_id'] = $owner->isCaptainAccount() ? $owner->parent_id : $owner->id;
//                $data['password'] = SignUpController::generateRandomString(); // Generate temporary password
//
//                /** @var User $member */
//                $member = Sentinel::register($data, false);
//
//                // Add role for user
//                /** @var Role $role */
//                $role = Sentinel::findRoleBySlug($request->get('role'));
//                $role->users()->attach($member);
//
//                // Make a profile
//                $memberProfile = new Profile();
//                $memberProfile->user_id = $member->id;
//                $memberProfile->saveOrFail();
//
//                if ($member->isCaptainAccount()) {
//                    $member->addToIndex();
//                }
//            }
//
//            // Assign member to vessel
//            $vessel->attachMember($member->id);
//
//            // Charge or Prolong extra team member offer if needed
//            try {
//                $custom = [
//                    'for_member_id' => $member->id
//                ];
//                if ($crewmembersCount >= $this->extraOfferRepository->getVesselTeamSlotsCount($vessel->id)) {
//                    // Charge for extra crew member slot
//                    $this->extraOfferRepository->chargeForExtraCrewMember($vessel, $custom);
//                } else {
//                    if ($crewmembersCount >= config('billing.vessel.free_crew_members_count')) {
//                        // Has paused offers, so prolong extra crew member slot
//                        $this->extraOfferRepository->prolongExtraCrewMember($vessel, $custom);
//                    }
//                }
//                // Other cases, has free crew member slots
//            } catch (\Exception $e) {
//                report($e);
//
//                throw new PaymentException('Failed to charge additional fee. ' . $e->getMessage(), $e->getCode(), $e);
//            }
//
//            DB::commit();
//        } catch (PaymentException $e) {
//            DB::rollback();
//
//            return redirect()
//                ->route('account.boat.crew.assign', ['boat_id' => $vessel->id])
//                ->withInput()
//                ->with('error', $e->getMessage());
//        } catch (\Throwable $e) {
//            DB::rollback();
//
//            return redirect()
//                ->route('account.boat.crew.assign', ['boat_id' => $vessel->id])
//                ->withInput()
//                ->with('error', trans('crew.failed_to_assign') . ' ' . $e->getMessage());
//        }
//
//        /**
//         * Sending welcome email
//         */
//        if (empty($userId)) {
//            $mail = new CrewMemberActivation($member);
//            Mail::send($mail);
//        }
//
//        /**
//         * Notification
//         */
//        $mail = new MemberAssigned($vessel, $member);
//        Mail::send($mail);
//
//        return redirect()->route('account.boat.crew.index', ['boat_id' => $vessel->id])->with('success', trans('crew.member_was_assigned', [
//            'member' => $member->full_name,
//            'vessel' => $vessel->name
//        ]));
//    }

    /**
     * @param int $related_member_id
     * @param int $member_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function assignMember($related_member_id, int $member_id)
    {
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);
        /** @var CrewMember $member */
        $member = $this->loadMember($related_member_id, $member_id);

        /** @var int $crewmembersCount */
        $crewmembersCount = $vessel->crew->count();

        DB::beginTransaction();
        try {
            // Assign member to vessel
            $vessel->attachMember($member->id);

            // Charge or Prolong extra team member offer if needed
            try {
                $custom = [
                    'for_member_id' => $member->id
                ];
                if ($crewmembersCount >= $this->extraOfferRepository->getVesselTeamSlotsCount($vessel->id)) {
                    // Charge for extra crew member slot
                    $this->extraOfferRepository->chargeForExtraCrewMember($vessel, $custom);
                } else {
                    if ($crewmembersCount >= config('billing.vessel.free_crew_members_count')) {
                        // Has paused offers, so prolong extra crew member slot
                        $this->extraOfferRepository->prolongExtraCrewMember($vessel, $custom);
                    }
                }
                // Other cases, has free crew member slots
            } catch (\Exception $e) {
                report($e);

                throw new PaymentException('Failed to charge additional fee. ' . $e->getMessage(), $e->getCode(), $e);
            }

            DB::commit();
        } catch (PaymentException $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', trans('crew.failed_to_assign') . ' ' . $e->getMessage());
        }

        /**
         * Notification
         */
        $mail = new MemberAssigned($vessel, $member);
        Mail::send($mail);

        return redirect()->back()->with('success', trans('crew.member_was_assigned', [
            'member' => $member->full_name,
            'vessel' => $vessel->name
        ]));
    }

    /**
     * @param int $related_member_id
     * @param int $member_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function unassignMember($related_member_id, int $member_id)
    {
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);
        /** @var CrewMember $member */
        $member = $this->loadMember($related_member_id, $member_id);

        if (Sentinel::getUser()->isCaptainAccount() && $member->isCaptainAccount()) {
            abort(403, "Captain can't unassign a captain");
        }

        /** @var VesselsCrew $link */
        $link = VesselsCrew::where('vessel_id', $vessel->id)->where('user_id', $member_id)->first();
        if (empty($link)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $link->delete();

            $this->extraOfferRepository->pauseExtraCrewMember($vessel);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->with('error', trans('crew.failed_to_unassign'));
        }

        /**
         * Notification
         */
        $mail = new MemberUnAssigned($vessel, $link->user);
        Mail::send($mail);

        return redirect()->back()->with('success', trans('crew.member_was_unassigned', [
            'member' => $link->user->full_name,
            'vessel' => $vessel->name
        ]));
    }

    /**
     * @param int $related_member_id
     * @param int $member_id
     */
    public function viewCV($related_member_id, int $member_id)
    {
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);
        /** @var CrewMember $member */
        $member = $this->loadMember($related_member_id, $member_id);

        if (!$member->profile->file_id) {
            abort(404);
        }

        response()->download($member->profile->file->getFilePath(), $member->profile->file->filename);
    }

    /**
     * @param $related_member_id
     * @param int $member_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeMember($related_member_id, int $member_id)
    {
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);
        /** @var CrewMember $member */
        $member = $this->loadMember($related_member_id, $member_id);

        if (Sentinel::getUser()->isCaptainAccount() && $member->isCaptainAccount()) {
            abort(403, "Captain can't delete a captain");
        }

        DB::beginTransaction();
        try {
            /** @var VesselsCrew $link */
            $link = VesselsCrew::where('vessel_id', $vessel->id)->where('user_id', $member_id)->first();
            if ($link) {
                $link->delete();
            }

            $member->delete();

            $this->extraOfferRepository->pauseExtraCrewMember($vessel);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->with('error', trans('crew.failed_to_delete'));
        }

        return redirect()->back()->with('success', trans('crew.member_was_deleted', [
            'member' => $member->full_name,
            'vessel' => $vessel->name
        ]));
    }

    /**
     * @param $id
     * @return Vessel
     */
    protected function loadVessel(int $id)
    {
        $vessel = Vessel::where('type', 'vessel')->where('user_id', $id)->first();
        if (!$vessel) {
            abort(404, 'Not found');
        }

        return $vessel;
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return CrewMember
     */
    protected function loadMember($related_member_id, int $id)
    {
        /** @var Vessel $vessel */
        $vessel = $this->loadVessel($related_member_id);

        $member = CrewMember::crewAccounts()->where('parent_id', $vessel->owner->id)->find($id);
        if (!$member) {
            abort(404, 'Not found');
        }

        return $member;
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function colorUpdate($related_member_id, Request $request)
    {
        $member = $this->loadMember($related_member_id, $request->get('user_id'));
        $color = strtolower($request->get('color'));
        if (!in_array($color, array_keys(Crew::colors()))) {
            abort(400);
        }

        $member->profile->color = $color;
        $member->profile->saveOrFail();

        return response()->json([]);
    }
}
