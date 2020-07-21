<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\Http\Requests\ContactRequest;
use App\Mail\Contact;
use App\Mail\ContactUser;
use Sentinel;
use Mail;

/**
 * Class ContactController
 * @package App\Http\Controllers
 */
class ContactController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact()
    {
        resolve('seotools')->setTitle(trans('general.contact'));

        return view('contact');
    }

    /**
     * @param ContactRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ContactRequest $request)
    {
        $data = [
            'contact-name' => $request->get('contact-name'),
            'contact-email' => $request->get('contact-email'),
            'contact-subject' => $request->get('contact-subject'),
            'contact-msg' => $request->get('message'),
        ];

        // Send email to admin
        Mail::send(new Contact($data));

        // Send email to user
        Mail::send(new ContactUser($data));

        return redirect(route('contact'))->with('success', trans('auth/message.contact.success'));
    }
}
