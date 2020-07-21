<?php

namespace App\Http\Controllers;

use App\EmailConfirmations;
use App\Helpers\Country;
use App\Helpers\Geocoding;
use App\Http\Requests\ProfileContactRequest;
use App\Http\Requests\ProfilePhotoRequest;
use App\Mail\EmailChanged;
use App\Mail\EmailConfirmation;
use Illuminate\Http\Request;
use App\Repositories\ServiceRepository;
use App\User;
use Illuminate\Support\MessageBag;
use Spatie\Newsletter\Newsletter as Mailchimp;
use Intervention\Image\Facades\Image;
use Validator;
use Sentinel;
use Mail;
use DB;

/**
 * Class ProfileController
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    protected $tabs = ['contact', 'photo', 'video', 'newsletter'];

    /**
     * @var Request|null
     */
    protected $request = null;

    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    public function __construct(MessageBag $messageBag, Request $request, ServiceRepository $serviceRepository)
    {
        parent::__construct($messageBag);

        $this->request = $request;

        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            $this->user = Sentinel::getUser();
        }
        return $this->user;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact()
    {
        /** @var User $user */
        $user = $this->getUser();

        $countries = Country::getOptions();

        return view('profile.contact', compact('user', 'countries'))->with('tabs', $this->tabs);
    }

    /**
     * @param ProfileContactRequest $request
     * @param Mailchimp $mailchimp
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function contactUpdate(ProfileContactRequest $request, Mailchimp $mailchimp)
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->fill($request->except('email', 'profile', 'subscribe'));

        if ($user->email != $request->get('email')) {
            DB::beginTransaction();

            try {
                $newEmail = $request->get('email');
                $confirmation = EmailConfirmations::where('user_id', $user->id)
                    ->where('email', $newEmail)
                    ->where('completed', 0)
                    ->first();
                if (empty($confirmation)) {
                    $confirmation = new EmailConfirmations();
                    $confirmation->user_id = $user->id;
                    $confirmation->email = $newEmail;
                }
                $confirmation->code = str_random(32);
                $confirmation->saveOrFail();

                Mail::send(new EmailConfirmation($user, $confirmation));

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();

                return redirect(route('profile.contact'))->withInput()->with('error', 'Failed to change email. ' . $e->getMessage());
            }
        }

        $user->profile->fill((array)request('profile', []));
        $user->profile->company_country = 'US';

        // Save geo location by address
        if ($user->getOriginal('address') != $user->getAttribute('address')
            || $user->getOriginal('city') != $user->getAttribute('city')
            || $user->getOriginal('user_state') != $user->getAttribute('user_state')
            || $user->getOriginal('country') != $user->getAttribute('country')) {
            $address = $user->full_address;
            $response = Geocoding::latlngLookup($address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = current($response->results);
                    $user->map_lat = $place->geometry->location->lat;
                    $user->map_lng = $place->geometry->location->lng;
                }
            }
        }

        if ($user->save() && $user->profile->save()) {
            $success = 'Contact information was successfully updated.';

            activity($user->full_name)
                ->performedOn($user)
                ->causedBy(Sentinel::getUser())
                ->log('User updated successfully');

            return redirect(route('profile.contact'))->with('success', $success);
        }

        return redirect(route('profile.contact'))->withInput()->with('error', trans('users/message.error.update'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function photo()
    {
        /** @var User $user */
        $user = $this->getUser();

        return view('profile.photo', compact('user'))->with('tabs', $this->tabs);
    }

    /**
     * @param ProfilePhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function photoUpdate(ProfilePhotoRequest $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $success = true;

        if ($request->hasFile('pic')) {
            $imageFileName = $this->processPicImage($request);
            if ($imageFileName) {
                $user->deleteImage(false);
                $user->pic = $imageFileName;
            } else {
                $success = $success && false;
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($user->saveOrFail()) {
            if ($success) {
                activity($user->full_name)
                    ->performedOn($user)
                    ->causedBy(Sentinel::getUser())
                    ->log('User photo updated successfully');

                $request->session()->flash('success', 'User saved successfully.');
            }
        }

        return redirect(route('profile.photo'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function deleteCompanyImage(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->deleteCompanyImage();

        activity($user->full_name)
            ->performedOn($user)
            ->causedBy(Sentinel::getUser())
            ->log('User company photo deleted successfully');

        $request->session()->flash('success', 'Company logo was successfully deleted.');

        return redirect(route('profile.photo'));
    }

    /**
     * @param Request $request
     * @return bool|null|string
     */
    protected function processPicImage(Request $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('pic')) {
                $file = $request->file('pic');

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

                return $fileName;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return bool|null|string
     */
    protected function processCompanyPicImage(Request $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('company_pic')) {
                $file = $request->file('company_pic');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/company/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                return $fileName;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return null;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function video()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->hasAccess(['profile.video'])) {
            abort(404);
        }

        return view('profile.video', compact('user'))->with('tabs', $this->tabs);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function videoUpdate(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->hasAccess(['profile.video'])) {
            abort(404);
        }
        $profile = $user->profile;

        $result = [];

        if ($request->hasfile('file')) {
            $file = $request->file('file');
            if ($profile->attachments()->where('type', 'video')->exists()) {
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => 'The video file already uploaded.'
                ];

                return response()->json(['file' => $result]);
            }

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'file', 'mimes:mp4', 'max:100000']
            ]);

            if ($validator->fails()) {
                $bag = $validator->getMessageBag();
                $message = $bag->first('file');
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $message
                ];

                return response()->json(['file' => $result]);
            }

            $storePath = 'profiles/videos/' . $profile->id;
            try {
                $fl = new \App\File();

                $fl->mime = $file->getMimeType();
                $fl->size = $file->getSize();
                $fl->filename = $file->getClientOriginalName();
                $fl->disk = 'video';
                $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                $fl->saveOrFail();

                $profile->attachFile($fl, 'video');

                unset($fl);

                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ];
            } catch (\Throwable $e) {
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $e->getMessage()
                ];
            } finally {
                if (isset($fl->id) && $fl->id) {
                    // Delete file in case if failed to update database
                    $fl->delete();
                }
            }
        }

        return response()->json(['file' => $result]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function videoDelete()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->hasAccess(['profile.video'])) {
            abort(404);
        }
        $profile = $user->profile;

        $link = $profile->attachments()->where('type', 'video')->first();
        if (!$link) {
            abort(404);
        }

        if ($link->delete()) {
            return redirect(route('profile.video'))->with('success', 'Video was successfully deleted.');
        }

        return redirect(route('profile.video'))->with('error', 'Failed to delete video file.');
    }

    /**
     * @param Mailchimp $mailchimp
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newsletter(Mailchimp $mailchimp)
    {
        /** @var User $user */
        $user = $this->getUser();

        $isSubscribed = $mailchimp->isSubscribed($user->email);
        $subscribeOptions = [];
        if ($isSubscribed) {
            $subscribeOptions['checked'] = 'checked';
        }

        return view('profile.newsletter', compact('user', 'isSubscribed', 'subscribeOptions'))->with('tabs', $this->tabs);
    }

    /**
     * @param Request $request
     * @param Mailchimp $mailchimp
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function newsletterUpdate(Request $request, Mailchimp $mailchimp)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->exists('subscribe')) {
            $isSubscribed = $mailchimp->isSubscribed($user->email);
            $subscribe = boolval($request->post('subscribe'));
            if ($isSubscribed != $subscribe) {
                if ($subscribe) {
                    $mailchimp->subscribeOrUpdate($user->email, ['firstName' => $user->first_name, 'lastName' => $user->last_name]);
                } else {
                    $mailchimp->unsubscribe($user->email);
                }

                if ($mailchimp->lastActionSucceeded()) {
                    activity($user->full_name)
                        ->performedOn($user)
                        ->causedBy(Sentinel::getUser())
                        ->log('User newsletter updated successfully');

                    if ($subscribe) {
                        $request->session()->flash('success', 'You subscribed to our newsletter.');
                    } else {
                        $request->session()->flash('success', 'You unsubscribed from our newsletter.');
                    }
                } else {
                    $request->session()->flash('error', 'Newsletter error ' . $mailchimp->getLastError());
                }
            }
        }

        return redirect(route('profile.newsletter'));
    }

    /**
     * @param $userId
     * @param $confirmationCode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function emailConfirmation($userId, $confirmationCode)
    {
        /** @var EmailConfirmations $confirmation */
        if ($confirmation = EmailConfirmations::active()->where('user_id', $userId)->where('code', $confirmationCode)->first()) {
            DB::beginTransaction();

            try {
                $confirmation->completed = true;
                $confirmation->completed_at = now();
                $confirmation->saveOrFail();

                $oldEmail = $confirmation->user->email;
                $confirmation->user->email = $confirmation->email;
                $confirmation->user->saveOrFail();

                Mail::send(new EmailChanged($confirmation->user, $confirmation, $oldEmail));

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();

                throw new \Exception('An unknown error occurred. ' . $e->getMessage(), $e->getCode(), $e);
            }

            return view('email-confirmed', compact('confirmation'));
        }

        abort(404);
    }
}
