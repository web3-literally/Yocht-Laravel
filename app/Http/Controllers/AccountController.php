<?php

namespace App\Http\Controllers;

use App\Helpers\RelatedProfile;
use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\Http\Requests\PasswordChangeRequest;
use App\User;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Sentinel;
use Storage;
use Image;

/**
 * Class AccountController
 * @package App\Http\Controllers
 */
class AccountController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $this->setDashboardTitle();

        $related = RelatedProfile::currentRelatedMember();

        if ($related) {
            if ($related->isBusinessAccount()) {
                return view('account.dashboard-business', compact('related'));
            } elseif($related->isVesselAccount() || $related->isTenderAccount()) {
                return view('account.dashboard-boat', compact('related'));
            }
        }

        return view('account.overview');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function overview()
    {
        resolve('seotools')->metatags()->setTitle(trans('general.overview'));

        return view('account.overview');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changePassword()
    {
        resolve('seotools')->metatags()->setTitle(trans('general.account_change_password'));

        $user = Sentinel::getUser();

        return view('account.change_password', compact('user'));
    }

    public function changePasswordUpdate(PasswordChangeRequest $request)
    {
        $user = Sentinel::getUser();
        $hasher = Sentinel::getHasher();

        $password = $request->get('new_password');
        if ($user->update([
            'password' => $hasher->hash($password)
        ])) {
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('User password updated successfully');

            $request->session()->flash('success', trans('passwords.password_changed'));
        } else {
            $request->session()->flash('error', trans('passwords.password_changed_failed'));
        }

        return redirect(route('account.change-password'));
    }

    /**
     * @param $fullpath
     * @return $this
     * @throws \Exception
     */
    protected function QRCodeGen($link, $fullpath) {
        $folder = dirname($fullpath);
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $qrCode = new QrCode();
        $qrCode
            ->setText($link)
            ->setSize(162)
            ->setPadding(0)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
            ->save($fullpath);

        return $this;
    }

    public function QRCode()
    {
        /** @var User $member */
        $member = Sentinel::getUser();

        $filename = request()->getHost() . '-member-profile-' . $member->id . '-qr-image.png';
        $filepath = 'qr/' . $filename;

        $fullpath = Storage::disk('public')->path($filepath);

        if (!file_exists($fullpath)) {
            $this->QRCodeGen($member->getPublicProfileLink(), $fullpath);
        }

        return Image::make($fullpath)->response();
    }

    public function QRCodeDownload()
    {
        /** @var User $member */
        $member = Sentinel::getUser();

        $filename = request()->getHost() . '-member-profile-' . $member->id . '-qr-image.png';
        $filepath = 'qr/' . $filename;

        $fullpath = Storage::disk('public')->path($filepath);

        if (!file_exists($fullpath)) {
            $this->QRCodeGen($member->getPublicProfileLink(), $fullpath);
        }

        return response()->download($fullpath);
    }
}
