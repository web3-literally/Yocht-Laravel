<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class UsersOnline extends \HighIdeas\UsersOnline\Middleware\UsersOnline
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
        $response = $next($request);

        if (Sentinel::check()) {
            Sentinel::getUser()->setCache(config('session.lifetime'));
        }

        return $response;
    }
}
