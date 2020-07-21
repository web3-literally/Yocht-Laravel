<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;
use Route;

class SentinelMemberOwner
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
            if (Sentinel::getUser()->isMemberOwnerAccount()) {
                return $next($request);
            }
            return abort(403);
        }
        return Redirect::route('signup');
    }
}
