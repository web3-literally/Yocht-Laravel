<?php

namespace App\Http\Middleware;

use App\Helpers\Permissions;
use Closure;

class SentinelCanContactTo
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
        if (Permissions::canContactTo()) {
            return $next($request);
        }

        return abort(403);
    }
}
