<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Sentinel;
use Closure;
use Illuminate\Http\Response;

class CheckPermission
{
    /**
     * @var Sentinel
     */
    protected $sentinel;

    /**
     * CheckPermission constructor.
     * @param Sentinel $sentinel
     */
    public function __construct(Sentinel $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($this->sentinel->check() && $this->sentinel->getUser()->hasAccess($permission)) {
            return $next($request);
        }

        return abort(404);
    }

}