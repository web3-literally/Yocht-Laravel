<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Sentinel;
use Closure;
use Illuminate\Http\Response;

/**
 * Class BackendUserRedirect
 *
 * Backend user has no member dashboard and dashboard functionality.
 *
 * @package App\Http\Middleware
 */
class BackendUserRedirect
{
    /**
     * @var Sentinel
     */
    protected $sentinel;

    /**
     * BackendUserRedirect constructor.
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
    public function handle($request, Closure $next)
    {
        if ($this->sentinel->check() && $this->sentinel->getUser()->hasAccess('admin')) {
            if (preg_match('/^dashboard|my-profile|profile|account|subscriptions/', $request->route()->getName())) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }

}