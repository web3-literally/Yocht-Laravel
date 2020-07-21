<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;
use Route;

class ApiQA
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (request('key') && request('key') === config('api.key')) {
            return $next($request);
        }

        return abort(403);
    }
}
