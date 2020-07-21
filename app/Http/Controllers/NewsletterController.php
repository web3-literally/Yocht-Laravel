<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Mail\NewsletterSubscription;
use Illuminate\Support\Facades\URL;
use Spatie\Newsletter\Newsletter as Mailchimp;
use Mail;

/**
 * Class NewsletterController
 * @package App\Http\Controllers
 */
class NewsletterController extends Controller
{
    /**
     * @param SubscribeRequest $request
     * @param Mailchimp $mailchimp
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function subscribe(SubscribeRequest $request, Mailchimp $mailchimp)
    {
        if (!env('MAILCHIMP_APIKEY')) {
            throw new \Exception('Newsletter widget doesn\'t configured');
        }

        $message = trans('newsletter.subscribed');

        $email = $request->post('email');

        $response = $mailchimp->subscribeOrUpdate($email);
        if (!$mailchimp->lastActionSucceeded()) {
            $message = $mailchimp->getLastError();

            if (!$request->ajax()) {
                return redirect(URL::route('home'))->with('error', $message);
            }
        }

        if ($request->ajax()) {
            Mail::to($email)->send(new NewsletterSubscription([]));
            return response()->json([
                'message' => $message
            ]);
        }

        return redirect(URL::route('home'))->with('success', $message);
    }
}
