<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelBackend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Sentinel::check())
            return redirect('admin/signin')->with('info', 'You must be logged in!');
        elseif(!Sentinel::getUser()->hasAccess(['admin']))
            return redirect()->route('home')->with('info', 'You don\'t have permissions!');

        return $next($request);
    }
}
