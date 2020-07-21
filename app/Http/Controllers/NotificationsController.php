<?php

namespace App\Http\Controllers;

use Sentinel;

/**
 * Class NotificationsController
 * @package App\Http\Controllers
 */
class NotificationsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $notifications = Sentinel::getUser()->notifications()->paginate(10);

        return view('notification.notifications', compact('notifications'));
    }
}