<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\User;
use Sentinel;

/**
 * Class AccountsController
 * @package App\Http\Controllers
 */
class AccountsController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setDashboardTitle(trans('accounts.accounts'));

        /** @var User $user */
        $user = Sentinel::getUser();

        $accounts = $user->accounts()->paginate(10);

        return view('accounts.index')->with('accounts', $accounts);
    }
}