<?php

namespace App\Http\Controllers;

use App\Helpers\Owner;
use App\Mail\Boats\Transfer\DestinationAcceptTransfer;
use App\Mail\Boats\Transfer\OriginConfirmTransfer;
use App\Mail\Boats\Transfer\Transferred;
use App\Mail\SignUp\MemberActivation;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselTransfer;
use App\Profile;
use App\Rules\IsOwnerMember;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Sentinel;
use View;
use Mail;
use DB;

/**
 * Class VesselsTransferController
 * @package App\Http\Controllers
 */
class VesselsTransferController extends Controller
{
    /**
     * @var null|Vessel
     */
    protected $_boat = null;

    /**
     * @var null|int
     */
    protected $_step = null;

    /**
     * @var array
     */
    protected $_steps = [];

    /**
     * @var null|array
     */
    protected $_data = null;

    /**
     * @param int $id
     * @return Vessel
     * @throws \Exception
     */
    protected function loadBoat($id)
    {
        $owner = Owner::currentOwner();

        $builder = Vessel::where('owner_id', $owner->getUserId());
        $vessel = $builder->find($id);
        if (!$vessel) {
            abort(404);
        }

        return $vessel;
    }

    /**
     * @return User
     */
    protected function loadMember()
    {
        if ($this->_data['member_id'] ?? null) {
            return User::findOrFail($this->_data['member_id']);
        }

        $member = new User();
        $member->email = $this->_data['member_email'];

        return $member;
    }

    /**
     * @return $this
     */
    protected function initSteps()
    {
        $this->_steps = [
            1 => 'Overview'
        ];
        $this->_steps[2] = 'Account';
        /*if ($this->_data['member_email'] ?? null) {
            $this->_steps[3] = 'Password';
        }*/
        if ($this->_boat->tenders->count()) {
            $this->_steps[4] = 'Tenders';
        }
        $this->_steps[5] = 'Settings';

        return $this;
    }

    /**
     * @param $related_member_id
     * @param $boat_id
     * @param $step
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function step($related_member_id, $boat_id, $step, Request $request)
    {
        $this->_boat = $this->loadBoat($boat_id);
        $this->_member = $this->loadBoat(request('boat_id'));

        $this->_step = $request->session()->get('transferStep', 1);
        if ($step > $this->_step) {
            return redirect()->route('account.boat.transfer.step', ['boat_id' => $boat_id, $this->_step]);
        }
        $this->_data = $request->session()->get('transferData', []);

        $this->initSteps();
        end($this->_steps);
        $last = key($this->_steps);
        reset($this->_steps);
        if ($step >= 1 && $step <= $last) {
            $next = null;
            if ($step == 3 /*&& !($this->_data['member_email'] ?? null)*/) {
                // Skip password step (odd step)
                $next = 4;
            }
            if ($step == 4 && !($this->_boat->tenders->count())) {
                $next = 5;
            }
            if ($next) {
                $request->session()->put('transferStep', $next);
                return redirect()->route('account.boat.transfer.step', ['boat_id' => $this->_boat->id, 'step' => $next]);
            }

            View::share('currentStep', $step);
            View::share('processStep', $this->_step);
            View::share('stepsList', $this->_steps);
            View::share('currentBoat', $this->_boat);
            View::share('currentData', $this->_data);

            if ($this->_data['tenders'] ?? null) {
                $ids = (array)$this->_data['tenders'];
                View::share('currentBoatTenders', Vessel::whereIn('id', $ids)->get());
            }

            if ($this->_data['transfer_date'] ?? null) {
                View::share('transferDate', $this->_data['transfer_date']);
            }

            $stepName = "step{$step}" . ($request->getMethod() == 'GET' ? '' : 'store');

            return $this->{$stepName}($request);
        }

        abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function step1(Request $request)
    {
        return view('vessels.transfer.step1');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function step1store(Request $request)
    {
        $next = 2;

        $request->session()->put('transferStep', $next);

        return redirect()->route('account.boat.transfer.step', ['boat_id' => $this->_boat->id, 'step' => $next]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function step2(Request $request)
    {
        return view('vessels.transfer.step2');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function step2store(Request $request)
    {
        $next = 3;

        $request->validate([
            'member_id' => ['required_without:member_email', 'nullable', 'numeric', resolve(IsOwnerMember::class)],
            'member_email' => ['required_without:member_id', 'nullable', 'email'],
        ]);

        if ($memberId = $request->get('member_id')) {
            $this->_data['member_id'] = $memberId;
            $this->_data['member_email'] = null;
        } else {
            $memberEmail = $request->get('member_email');
            $exists = DB::table('users')->join('role_users', 'role_users.user_id', '=', 'users.id')->join('roles', 'roles.id', '=', 'role_users.role_id')
                ->where('users.id', '!=', Sentinel::getUser()->getUserId())
                ->where('roles.slug', 'owner')
                ->where('email', $memberEmail)
                ->select('users.id')
                ->first();
            if ($exists) {
                $this->_data['member_id'] = $exists->id;
                $this->_data['member_email'] = null;
            } else {
                $this->_data['member_id'] = null;
                $this->_data['member_email'] = $memberEmail;
            }
        }

        $request->session()->put('transferData', $this->_data);
        $request->session()->put('transferStep', $next);

        return redirect()->route('account.boat.transfer.step', ['boat_id' => $this->_boat->id, 'step' => $next]);
    }

    /**
     * @deprecated
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function step3(Request $request)
    {
        return view('vessels.transfer.step3');
    }

    /**
     * @deprecated
     * @param Request $request
     */
    protected function step3store(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function step4(Request $request)
    {
        return view('vessels.transfer.step4');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function step4store(Request $request)
    {
        $next = 5;

        $ids = $this->_boat->tenders->pluck('id')->all();

        $request->validate([
            'tenders' => ['nullable'],
            'tenders.*' => ['numeric', Rule::in($ids)],
        ]);

        $this->_data['tenders'] = $request->get('tenders', []);

        $request->session()->put('transferData', $this->_data);
        $request->session()->put('transferStep', $next);

        return redirect()->route('account.boat.transfer.step', ['boat_id' => $this->_boat->id, 'step' => $next]);
    }

    /**
     * Transfer settings
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function step5(Request $request)
    {
        View::share('transferDate', old('transfer_date',$this->_data['transfer_date'] ?? date('Y-m-d')));

        return view('vessels.transfer.step5');
    }

    /**
     * Transfer settings store
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function step5store(Request $request)
    {
        $request->validate([
            'transfer_date' => ['required', 'date', 'date_format:Y-m-d', 'after:-1day'],
        ]);

        $this->_data['transfer_date'] = $request->get('transfer_date');

        $this->_data['origin_confirm_key'] = uniqid();
        $this->_data['destination_confirm_key'] = uniqid();

        DB::beginTransaction();

        try {
            $holder = $this->_boat->owner;
            $member = $this->loadMember();

            if (empty($member->id)) {
                // Transfer to a new member
                $member = Sentinel::register([
                    'email' => $member->email,
                    'password' => SignUpController::generateRandomString()
                ], false);
                $role = Sentinel::findRoleBySlug('owner');
                $role->users()->attach($member);
                $profile = new Profile();
                $profile->user_id = $member->id;
                $profile->fill([]);
                $profile->saveOrFail();

                $member->addToIndex();

                Mail::send(new MemberActivation($member));
            }

            $transfer = new VesselTransfer();
            $transfer->boat_id = $this->_boat->id;
            $transfer->origin_member_id = $holder->id;
            $transfer->destination_member_id = $member->id;
            $transfer->transfer_date = $this->_data['transfer_date'];
            $transfer->status = 'pending';
            $transfer->data = $this->_data;
            $transfer->saveOrFail();

            Mail::send(new OriginConfirmTransfer($transfer, $this->_data['origin_confirm_key']));

            if (Carbon::parse($transfer->transfer_date)->isToday()) {
                Mail::send(new DestinationAcceptTransfer($transfer, $this->_data['destination_confirm_key']));
            } else {
                $days = Carbon::now()->diffInDays(Carbon::parse($transfer->transfer_date)) + 1;
                $seconds = Carbon::now()->addDay($days)->diffInSeconds(Carbon::now());
                Mail::later($seconds, new DestinationAcceptTransfer($transfer, $this->_data['destination_confirm_key']));
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to prepare vessel transfer. ' . $e->getMessage());
        }

        $request->session()->put('transferData', []);
        $request->session()->put('transferStep', 1);

        return redirect()->route('account.boat.transfer.details', ['boat_id' => $transfer->boat_id, 'transfer_id' => $transfer->id])->with('success', 'We\'ve sent you a confirmation email. Please, check your mail inbox.');
    }

    /**
     * @param int $related_member_id
     * @param int $boat_id
     * @param int $transfer_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function details($related_member_id, $boat_id, $transfer_id)
    {
        $transfer = VesselTransfer::my()->findOrFail($transfer_id);

        View::share('currentBoat', $transfer->boat);
        View::share('currentMember', $transfer->destination);
        View::share('currentData', $transfer->data);

        if ($transfer->data['tenders'] ?? null) {
            $ids = (array)$transfer->data['tenders'];
            View::share('currentBoatTenders', Vessel::whereIn('id', $ids)->get());
        }

        if ($transfer->data['transfer_date'] ?? null) {
            View::share('transferDate', $transfer->data['transfer_date']);
        }

        return view('vessels.transfer.details');
    }

    /**
     * @param int $transfer_id
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    protected function transferConfirm($transfer_id, $key)
    {
        $transfer = VesselTransfer::find($transfer_id);
        if ($transfer->data['origin_confirm_key'] && $transfer->data['origin_confirm_key'] == $key) {
            if ($transfer->origin_confirmed) {
                abort(404);
            }

            $transfer->origin_confirmed = true;
            $transfer->saveOrFail();

            return view('vessels.transfer.transfer-confirmed', compact('transfer'));
        }

        abort(404);
    }

    /**
     * @param int $transfer_id
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    protected function transferAccept($transfer_id, $key)
    {
        $transfer = VesselTransfer::find($transfer_id);
        if ($transfer->data['origin_confirm_key'] && $transfer->data['destination_confirm_key'] == $key) {
            if ($transfer->destination_confirmed) {
                abort(404);
            }

            $transfer->destination_confirmed = true;
            $transfer->saveOrFail();

            return view('vessels.transfer.transfer-accepted', compact('transfer'));
        }

        abort(404);
    }
}
