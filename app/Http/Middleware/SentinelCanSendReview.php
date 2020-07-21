<?php

namespace App\Http\Middleware;

use App\Helpers\Permissions;
use Closure;

class SentinelCanSendReview
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
        if (Permissions::canSendReview()) {
            return $next($request);
        }

        return abort(403);
    }
}
