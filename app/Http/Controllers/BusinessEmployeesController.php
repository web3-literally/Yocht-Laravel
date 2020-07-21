<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignEmployeeMemberRequest;
use App\Models\Business\BusinessInterface;
use Intervention\Image\Facades\Image;
use App\Employee;
use App\Helpers\Owner;
use App\Mail\SignUp\EmployeeActivation;
use App\Models\Business\Business;
use App\Profile;
use App\Role;
use App\User;
use Igaster\LaravelCities\Geo;
use Illuminate\Support\MessageBag;
use Sentinel;
use Mail;
use DB;

/**
 * Class BusinessEmployeesController
 * @package App\Http\Controllers
 */
class BusinessEmployeesController extends Controller
{
    /**
     * BusinessEmployeesController constructor.
     * @param MessageBag $messageBag
     */
    public function __construct(MessageBag $messageBag)
    {
        parent::__construct($messageBag);
    }

    /**
     * @param int $business_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(int $business_id)
    {
        /** @var Business $business */
        $business = $this->loadBusiness($business_id);

        resolve('seotools')->setTitle(trans('businesses.businesses') . config('seotools.meta.defaults.separator') . $business->name);

        $employees = $business->employees()->paginate(10);

        return view('businesses.employees.index', compact('business', 'employees'));
    }

    /**
     * @param int $business_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function assign(int $business_id)
    {
        /** @var Business $business */
        $business = $this->loadBusiness($business_id);

        resolve('seotools')->setTitle(trans('crew.assign_member') . config('seotools.meta.defaults.separator') . $business->name);

        $roles = Role::whereIn('slug', Employee::EMPLOYEE_ROLES)->pluck('name', 'slug')->all();
        $countries = Geo::getCountries()->pluck('name', 'country')->all();

        return view('businesses.employees.assign', compact('business', 'roles', 'countries'));
    }

    /**
     * @param int $business_id
     * @param AssignEmployeeMemberRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function assignMember(int $business_id, AssignEmployeeMemberRequest $request)
    {
        /** @var User $owner */
        $owner = Sentinel::getUser();

        /** @var Business $business */
        $business = $this->loadBusiness($business_id);

        // Register crew member
        DB::beginTransaction();

        try {
            $data = $request->only(['email', 'first_name', 'last_name', 'phone', 'country']);
            $data['parent_id'] = $owner->id;
            $data['password'] = SignUpController::generateRandomString(); // Generate temporary password

            /** @var User $member */
            $member = Sentinel::register($data, false);

            // Add role for user
            /** @var Role $role */
            $role = Sentinel::findRoleBySlug($request->get('role'));
            $role->users()->attach($member);

            // Make a member profile
            $memberProfile = new Profile();
            $memberProfile->user_id = $member->id;
            $memberProfile->saveOrFail();

            // Assign member to business
            $business->employees()->attach($member);

            // Send member's activation link
            if (empty($userId)) {
                $mail = new EmployeeActivation($member);
                Mail::send($mail);
            }

            // Process the photo
            if ($request->hasFile('photo')) {
                $defaultImgFormat = config('app.default_image_format');

                $file = $request->file('photo');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/users/';

                $temp = $file->move($destinationPath, $fileName);
                $uploadedFilePath = $destinationPath . $fileName;

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    $uploadedFilePath = $destinationPath . $fileName;
                    unlink($temp);
                }

                User::where('id', $member->id)->update(['pic' => $fileName]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            if (isset($uploadedFilePath)) {
                unlink($uploadedFilePath);
            }

            return back()->withInput()->with('error', trans('employees.failed_to_create') . ' ' . $e->getMessage());
        }

        return redirect()->route('account.businesses.employees.index', ['business_id' => $business->id])->with('success', trans('employees.member_was_assigned', [
            'member' => $member->full_name,
            'business' => $business->name
        ]));
    }

    /**
     * @param int $business_id
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(int $business_id, int $id)
    {
        /** @var Business $business */
        $business = $this->loadBusiness($business_id);
        if ($member = $business->employees()->where('businesses_employees.user_id', $id)->first()) {
            $member->delete();
            return back()->with('success', trans('employees.member_was_removed'));
        }

        abort(404);
    }

    /**
     * @param $id
     * @return Business
     * @throws \Exception
     */
    protected function loadBusiness(int $id)
    {
        $owner = Owner::currentOwner();

        $business = Business::where('owner_id', $owner->getUserId())->find($id);
        if (!$business) {
            throw new \Exception('Not found', 404);
        }

        return $business;
    }
}
