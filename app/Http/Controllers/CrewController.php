<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileContactRequest;
use App\Http\Requests\ProfilePhotoRequest;
use App\Role;
use Illuminate\Http\Request;
use Spatie\Newsletter\Newsletter as Mailchimp;
use App\User;
use Sentinel;

/**
 * Class CrewController
 * @package App\Http\Controllers
 */
class CrewController extends ProfileController
{
    protected $tabs = ['contact', 'photo'];

    /**
     * @return User
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            $query = User::crewAccounts();
            if (Sentinel::getUser()->isCaptainAccount()) {
                $roleTable = (new Role())->getTable();
                $query->where($roleTable . '.slug', '!=', 'captain');
            }
            $this->user = $query->find(request('user_id'));
            if (!$this->user) {
                abort(404);
            }
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

    /**
     * @param Request $request
     * @param Mailchimp $mailchimp
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function newsletterUpdate(Request $request, Mailchimp $mailchimp)
    {
        parent::newsletterUpdate($request, $mailchimp);

        return $this->redirect();
    }
}
