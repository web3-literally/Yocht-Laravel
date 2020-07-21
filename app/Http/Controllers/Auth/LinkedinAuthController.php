<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\MessageBag;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Redirect;
use Sentinel;

class LinkedinAuthController extends Controller
{
    protected $messageBag = null;

    /**
     * Initializer.
     *
     */
    public function __construct()
    {
        $this->messageBag = new MessageBag;

    }

    public function redirectToProvider()
    {
        return Socialite::driver('linkedin')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('linkedin')->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }
        $array = User::withTrashed()->where([
            ['email', '=', $user->email],
            ['deleted_at', '!=', null]
        ])->get();
        return $array->isEmpty()
            ? $this->findOrCreateUser($user, 'linkedin')
            : $this->sendFailedResponse("You are banned.");
    }

    protected function sendFailedResponse($msg = null)
    {
        return redirect('signin')->with(['msg' => $msg ?: 'Unable to login, try with another provider to login.']);
    }

    public function findOrCreateUser($providerUser, $provider)
    {
        $name = $providerUser->name;
        $splitName = explode(' ', $name);
        $first_name = '';
        $last_name = $splitName[count($splitName) - 1];
        for ($i = 0; $i <= count($splitName) - 2; $i++) {
            $first_name = $first_name . $splitName[$i] . ' ';
        }
        // check for already has account
        $user = User::where('email', $providerUser->email)->first();

        // if user already found
        if (!$user) {
            $user = User::create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $providerUser->email,
                'pic' => $providerUser->avatar,
                'provider' => $provider,
                'provider_id' => $providerUser->id
            ]);
            $role = Sentinel::findRoleById(2);

            if ($role) {
                $role->users()->attach($user);
            }
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('Registered');
            if (Activation::completed($user) == false) {

                $activation = Activation::create($user);
                Activation::complete($user, $activation->code);

            }
        }
        activity($user->full_name)
            ->performedOn($user)
            ->causedBy($user)
            ->log('Logged In');
        try {
            if (Sentinel::authenticate($user)) {
                return Redirect::route("account.dashboard")->with('success', 'Please update Password');
            }
        }catch (NotActivatedException $e) {
            $this->messageBag->add('email', trans('auth/message.account_not_activated'));
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $this->messageBag->add('email', trans('auth/message.account_suspended', compact('delay')));
        }
        return Redirect::route('signin')->withInput()->withErrors($this->messageBag);
    }
}
