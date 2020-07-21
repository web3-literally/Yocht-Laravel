<?php

namespace App\Http\Controllers;

use App\User;
use Sentinel;

/**
 * Class RelatedNotificationsController
 * @package App\Http\Controllers
 */
class RelatedNotificationsController extends Controller
{
    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($related_member_id)
    {
        $member = User::findOrFail($related_member_id);
        $notifications = $member->notifications()->paginate(10);

        return view('notification.notifications', compact('notifications'));
    }
}