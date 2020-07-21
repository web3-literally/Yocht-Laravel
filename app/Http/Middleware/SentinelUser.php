<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;
use Route;

class SentinelUser
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
        if (!Sentinel::check()) {
            if ($request->ajax()) {
                return abort(403);
            } else {
                return Redirect::route('signin', ['return' => $request->url()]);
            }
        }
        return $next($request);
    }
}
