<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;

class PlansController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('plans.index')->with(['plans' => Plan::active()->forCurrentMember()->orderBy('billing_frequency', 'desc')->get()]);
    }
}
