<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;
use Route;

class SentinelSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Sentinel::check()) {
            if (Sentinel::getUser()->isMemberAccount() && Sentinel::getUser()->hasMembership()) {
                return $next($request);
            } elseif (Sentinel::getUser()->isCaptainAccount()) {
                // Captain has access to owner vessels and crew if owner account subscribed
                if (!Sentinel::getUser()->parent->hasMembership()) {
                    return abort(403, 'Yacht Account expired. Please, contact to yacht owner for details.');
                }
                return $next($request);
            } elseif (Sentinel::getUser()->isMemberAccount()) {
                return Redirect::route('subscription.plans');
            } elseif (Sentinel::getUser()->isFreeAccount()) {
                return abort(403, 'Your free account doesn\'t allow access to this page');
            }
            return abort(403);
        }
        return Redirect::route('signup');
    }
}
