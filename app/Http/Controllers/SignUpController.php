<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUp\ConfirmPasswordRequest;
use App\Http\Requests\SignUp\SignUpRequest;
use App\Mail\ForgotPassword\ForgotPassword;
use App\Mail\SignUp\FreeUserActivation;
use App\Mail\SignUp\MemberActivation;
use App\Mail\Welcome;
use App\Profile;
use App\User;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use App\Plan;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Sentinel;
use Redirect;
use Mail;
use DB;

class SignUpController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        resolve('seotools')->setTitle(trans('general.sign_up'));

        $yachtOwnerPlans = Plan::active()->where('slug', 'like', 'yacht-owner%')->orderBy('billing_frequency', 'asc')->get();
        $marineContractorPlans = Plan::active()->where('slug', 'like', 'marine-contractor%')->orderBy('billing_frequency', 'asc')->get();

        $tab = $request->get('active', 'signup');

        return view('signup.index', compact('yachtOwnerPlans', 'marineContractorPlans', 'tab'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function authenticate(Request $request)
    {
        try {
            if ($user = Sentinel::authenticate(['email' => $request->get('signin_email'), 'password' => $request->get('signin_password')], $request->get('remember-me', 0))) {
                //Activity log for login
                activity($user->full_name)
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('LoggedIn');

                return redirect(route('dashboard'));
            } else {
                return redirect(route('signup', ['active' => 'signin']))->with('error', 'Email or password is incorrect.');
            }

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

        return redirect(route('signup', ['active' => 'signin']))->withInput()->withErrors($this->messageBag);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function forgotPassword(Request $request)
    {
        if (!$request->get('forgotpassword_email')) {
            return redirect(route('signup', ['active' => 'forgot-password']))->with('error', trans('auth/message.account_email_not_found'));
        }

        $user = Sentinel::findByCredentials(['email' => $request->get('forgotpassword_email')]);
        if ($user) {
            if ($activation = Activation::completed($user)) {
                try {
                    // Send the activation code
                    Mail::send(new ForgotPassword($user));
                } catch (\Throwable $e) {
                    // Even though the email was not found, we will pretend
                    // we have sent the password reset code through email,
                    // this is a security measure against hackers.
                    if (config('app.env') != 'production') {
                        throw new \Exception($e->getMessage(), $e->getCode(), $e);
                    }
                }
            }
        }

        return redirect(route('forgot-password-success'));
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param SignUpRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signupMember(SignUpRequest $request)
    {
        DB::beginTransaction();

        try {
            // Register a free user
            $data = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'dob' => $request->get('dob'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => self::generateRandomString() // Generate temporary password
            ];
            /** @var User $user */
            $user = Sentinel::register($data, false);

            $accountType = $request->get('account_type');

            // Add role for free user
            switch ($accountType) {
                case 'yacht-owner':
                    $role = Sentinel::findRoleBySlug('owner');
                    break;
                case 'marine-contractor':
                    $role = Sentinel::findRoleBySlug('marine');
                    break;
                default:
                    // Free
                    $role = Sentinel::findRoleBySlug('user');
            }
            $role->users()->attach($user);

            // Make a Customer
            $customer = $user->createAsBraintreeCustomer();
            $user->saveOrFail();

            // Process the photo
            if ($request->hasFile('photo')) {
                $defaultImgFormat = config('app.default_image_format');
                try {
                    if ($request->hasFile('photo')) {
                        $file = $request->file('photo');

                        $extension = $file->extension();
                        $hash = uniqid();
                        $fileName = $hash . '.' . $extension;
                        $destinationPath = public_path() . '/uploads/users/';

                        $temp = $file->move($destinationPath, $fileName);

                        if ($extension != $defaultImgFormat) {
                            $fileName = $hash . '.' . $defaultImgFormat;
                            Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                            unlink($temp);
                        }

                        User::where('id', $user->id)->update(['pic' => $fileName]);
                    }
                } catch (\Throwable $e) {
                    $request->session()->flash('warning', 'Failed to process image file.');
                    throw new \Exception('Failed to process image file.', '500', $e);
                }
            }

            // Make a profile
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();

            // Add new member to index
            $user->addToIndex();

            DB::commit();

            // Send an activation email
            switch ($accountType) {
                case 'yacht-owner':
                case 'marine-contractor':
                    $mail = new MemberActivation($user);
                    $route = route('activate-member-success');
                    break;
                default:
                    // Free
                    $mail = new FreeUserActivation($user);
                    $route = route('activate-free-success');
            }
            Mail::send($mail);

        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if (isset($customer)) {
                $user->deleteBraintreeCustomer();
            }

            if (isset($fileName) && file_exists($destinationPath . $fileName)) {
                // Cleanup image if failed to create an account
                unlink($destinationPath . $fileName);
            }

            return redirect(route('signup', ['active' => 'signup']))->withInput()->with('error', trans('auth/message.signup.error'));
        }

        return redirect($route);
    }

    /**
     * @param int $userId
     * @param string $activationCode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function activateForm($userId, $activationCode)
    {
        if (Sentinel::check()) {
            return Redirect::route('dashboard');
        }

        $user = User::find($userId);
        if (!$user) {
            abort(404);
        }

        if ($user->isMemberAccount()) {
            resolve('seotools')->setTitle(trans('general.activate_member_account'));
        } else {
            resolve('seotools')->setTitle(trans('general.activate_free_account'));
        }

        if (Activation::completed($user)) {
            return view('signup.activate.already-activated', compact('userId', 'activationCode'));
        }

        return view('signup.activate.form', compact('userId', 'activationCode'));
    }

    /**
     * @param ConfirmPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function activateMember(ConfirmPasswordRequest $request)
    {
        if (Sentinel::check()) {
            return Redirect::route('dashboard');
        }

        /** @var User $user */
        $user = User::findOrFail($request->get('user_id'));
        if (Activation::complete($user, $request->get('activation_code'))) {
            $hasher = Sentinel::getHasher();

            $password = $request->get('new_password');
            if (!$user->update([
                'password' => $hasher->hash($password)
            ])) {
                $request->session()->flash('error', trans('passwords.password_changed_failed'));
            }

            if ($user->isCaptainAccount() || $user->isCrewAccount()) {
                Mail::send(new Welcome($user));

                if ($user->asCrewMember()->isInCrew()) {
                    Sentinel::authenticate([
                        'email' => $user->email,
                        'password' => $password
                    ]);

                    return Redirect::route('dashboard')->with('success', trans('auth/message.activate.success'));
                } else {
                    return Redirect::route('signin')->with('success', trans('auth/message.activate.success'));
                }
            } else {
                Sentinel::authenticate([
                    'email' => $user->email,
                    'password' => $password
                ]);

                Mail::send(new Welcome($user));

                /*if ($user->isMemberAccount()) {
                    if (!$user->hasMembership()) {
                        return Redirect::route('subscription.plans')->with('success', trans('auth/message.activate.success'));
                    }
                }*/
            }

            return Redirect::route('dashboard')->with('success', trans('auth/message.activate.success'));
        } else {
            return Redirect::route('signin')->with('error', trans('auth/message.activate.error'));
        }
    }
}
