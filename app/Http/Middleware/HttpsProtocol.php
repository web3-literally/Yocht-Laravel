<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocol
{
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && in_array(App::environment(), ['production', 'development'])) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}