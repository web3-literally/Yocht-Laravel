<?php

namespace App\Http\Controllers;

use App\Repositories\ExtraCrewOfferRepository;
use App\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Http\Request;
use Redirect;
use Sentinel;
use View;

class FrontEndController extends Controller
{
    /**
     * Account sign in.
     *
     * @return View
     */
    public function getLogin()
    {
        // Is the user logged in?
        if (Sentinel::check()) {
            return Redirect::route('dashboard');
        }

        resolve('seotools')->setTitle(trans('general.sign_in'));

        // Show the login page
        return view('signin');
    }

    /**
     * Account sign in form processing.
     *
     * @return Redirect
     */
    public function postLogin(Request $request)
    {
        try {
            // Check extra offer slots for crew
            /** @var User $model */
            if ($model = Sentinel::authenticate($request->only('email', 'password'), $request->get('remember-me', 0), false)) {
                if ($model->hasAccess('admin')) {
                    $this->messageBag->add('email', trans('auth/message.account_not_found'));
                    return Redirect::back()->withInput()->withErrors($this->messageBag);
                }

                if ($model->isCaptainAccount() || $model->isCrewAccount()) {
                    if (!$model->parent->hasMembership()) {
                        return redirect('signin')->withInput()->with('error', 'You can\'t signin. Please, contact to your yacht owner.');
                    }
                }

                if ($model->isCrewAccount()) {
                    if ($vessel = $model->asCrewMember()->vessel) {
                        /** @var ExtraCrewOfferRepository $extraOfferRepository */
                        $extraOfferRepository = resolve('App\Repositories\ExtraCrewOfferRepository');
                        $vesselTeamSlotsCount = $extraOfferRepository->getVesselTeamSlotsCount($vessel->id);
                        if ($vessel->crew->count() > $vesselTeamSlotsCount) {
                            return redirect('signin')->withInput()->with('error', 'You can\'t signin. Please, contact to your captain or yacht owner.');
                        }
                    } else {
                        return redirect('signin')->withInput()->with('error', 'You haven\'t assigned to any boat');
                    }
                }

                if ($model->isMemberAccount()) {
                    // If Yacht owner or Marine Contractor haven't subscription after 30 days they become inactive
                    // TODO: Make member inactive status. Notice: user keep session during 30 days an more, he will never become inactive
                    if (!$model->hasMembership() && $model->created_at->diffInDays(Carbon::now()) >= 30) {
                        return redirect('signin')->withInput()->with('error', 'Your account inactive.');
                    }
                }

                // Try to log the user in
                if ($user = Sentinel::authenticate($request->only('email', 'password'), $request->get('remember-me', 0))) {
                    //Activity log for login
                    activity($user->full_name)
                        ->performedOn($user)
                        ->causedBy($user)
                        ->log('LoggedIn');

                    /** We don't need redirect to prev page, always to dashboard after login */
                    /*if ($request->get('return') && !preg_match('/forgot-password/i', $request->get('return'))) {
                        return redirect($request->get('return'));
                    }*/

                    return redirect(route('dashboard'));
                }
            }

            $this->messageBag->add('email', trans('auth/message.account_not_found'));
        } catch (UserNotFoundException $e) {
            $this->messageBag->add('email', trans('auth/message.account_not_found'));
        } catch (NotActivatedException $e) {
            $this->messageBag->add('email', trans('auth/message.account_not_activated'));
        } catch (UserSuspendedException $e) {
            $this->messageBag->add('email', trans('auth/message.account_suspended'));
        } catch (UserBannedException $e) {
            $this->messageBag->add('email', trans('auth/message.account_banned'));
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $this->messageBag->add('email', trans('auth/message.account_suspended', compact('delay')));
        }

        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);
    }

    /**
     * @return Redirect
     */
    public function logout()
    {
        if (Sentinel::check()) {
            //Activity log
            $user = Sentinel::getuser();
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('LoggedOut');
            // Log the user out
            Sentinel::logout();
        }
        // Redirect to the users page
        return redirect(route('home'))->with('success', 'You have successfully logged out!');
    }


}
