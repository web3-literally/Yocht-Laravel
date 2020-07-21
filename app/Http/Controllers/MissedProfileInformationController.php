<?php

namespace App\Http\Controllers;

use App\Helpers\Country;
use App\Rules\Address;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Sentinel;

/**
 * Class MissedProfileInformationController
 * @package App\Http\Controllers
 */
class MissedProfileInformationController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Sentinel::getUser();
        $countries = Country::getOptions();

        return view('missed-profile-information.index', compact('user', 'countries'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $request->validate([
            'first_name' => 'required|min:3|max:191',
            'last_name' => 'required|min:3|max:191',
            'phone' => 'required|max:191|phone:AUTO',
            'country' => 'required|' . Rule::in(array_keys(Country::getAll())),
            'user_state' => 'required|min:3|max:191',
            'city' => 'required|min:3|max:191',
            'address' => ['required', 'min:3', 'max:191', resolve(Address::class)],
        ]);

        $user->fill($request->only(['first_name', 'last_name', 'phone', 'country', 'user_state', 'city', 'address']));
        $user->saveOrFail();

        return redirect()->route('account.dashboard')->with('success', 'Profile was successfully updated.');
    }
}
