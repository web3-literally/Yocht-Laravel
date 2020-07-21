<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Sentinel;
use Closure;
use Illuminate\Http\Response;

class DenyRole
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
     * @param string ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if ($this->sentinel->check() && in_array($this->sentinel->getUser()->getAccountType(), $roles)) {
            return abort(403);
        }

        return $next($request);
    }

}