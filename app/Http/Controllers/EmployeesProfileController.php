<?php

namespace App\Http\Controllers;

use App\Helpers\Owner;
use App\Http\Requests\ProfileContactRequest;
use App\Http\Requests\ProfilePhotoRequest;
use App\Models\Business\Business;
use Spatie\Newsletter\Newsletter as Mailchimp;
use App\User;

/**
 * Class EmployeesProfileController
 * @package App\Http\Controllers
 */
class EmployeesProfileController extends ProfileController
{
    protected $tabs = ['contact', 'photo'];

    /**
     * @return User
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            $businessId = $this->request->route('business_id');
            $memberId = $this->request->route('user_id');
            $owner = Owner::currentOwner();

            $business = Business::where('owner_id', $owner->getUserId())->find($businessId);
            if (!$business) {
                abort(404);
            }

            $member = $business->employees()->where('businesses_employees.user_id', $memberId)->first();
            if (!$member) {
                abort(404);
            }

            $this->user = $member;
        }

        return $this->user;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirect()
    {
        $parts = explode('.', \Request::route()->getName());
        unset($parts[count($parts) - 1]);

        return redirect(route(implode('.', $parts), \Request::route()->parameters));
    }

    /**
     * @param ProfileContactRequest $request
     * @param Mailchimp $mailchimp
     * @return mixed
     */
    public function contactUpdate(ProfileContactRequest $request, Mailchimp $mailchimp)
    {
        parent::contactUpdate($request, $mailchimp);

        return $this->redirect();
    }

    /**
     * @param ProfilePhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function photoUpdate(ProfilePhotoRequest $request)
    {
        parent::photoUpdate($request);

        return $this->redirect();
    }
}
