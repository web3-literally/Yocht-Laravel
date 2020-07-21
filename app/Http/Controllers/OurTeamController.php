<?php

namespace App\Http\Controllers;

class OurTeamController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        resolve('seotools')->setTitle(trans('general.our_team'));

        return view('our-team.index');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function member($id)
    {
        resolve('seotools')->setTitle('Jonathan Barnes' . config('seotools.meta.defaults.separator') . trans('general.our_team'));

        return view('our-team.member')->with('id', $id);
    }
}
