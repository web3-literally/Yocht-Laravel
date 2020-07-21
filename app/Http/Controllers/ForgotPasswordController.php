<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Mail\ForgotPassword\ForgotPassword;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Sentinel;
use Redirect;
use Reminder;
use Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Forgot password page.
     *
     * @return View
     */
    public function getForgotPassword()
    {
        resolve('seotools')->setTitle(trans('general.forgot_password'));

        return view('forgot-password.forgot-password');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function postForgotPassword(Request $request)
    {
        if (!$request->get('email')) {
            return redirect(route('forgot-password'))->with('error', trans('auth/message.account_email_not_found'));
        }

        $user = Sentinel::findByCredentials(['email' => $request->get('email')]);
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
     * Forgot Password Confirmation page.
     *
     * @param Request $request
     * @param int $userId
     * @param string $passwordResetCode
     * @return View
     */
    public function getForgotPasswordConfirm(Request $request, $userId, $passwordResetCode = null)
    {
        resolve('seotools')->setTitle(trans('general.forgot_password'));

        if (!$user = Sentinel::findById($userId)) {
            return Redirect::route('forgot-password')->with('error', trans('auth/message.account_not_found'));
        }

        if ($reminder = Reminder::exists($user)) {
            if ($passwordResetCode == $reminder->code) {
                return view('forgot-password.form', compact(['userId', 'passwordResetCode']));
            }
        }

        return abort(404);
    }

    /**
     * Forgot Password Confirmation form processing page.
     *
     * @param PasswordResetRequest $request
     * @param int $userId
     * @param string $passwordResetCode
     * @return Redirect
     */
    public function postForgotPasswordConfirm(PasswordResetRequest $request, $userId, $passwordResetCode = null)
    {
        if (!$user = Sentinel::findById($userId)) {
            return abort(404);
        }

        if (!$reminder = Reminder::complete($user, $passwordResetCode, $request->get('password'))) {
            return Redirect::route('signup')->with('error', trans('auth/message.forgot-password-confirm.error'));
        }

        return Redirect::route('signin')->with('success', trans('auth/message.forgot-password-confirm.success'));
    }
}
