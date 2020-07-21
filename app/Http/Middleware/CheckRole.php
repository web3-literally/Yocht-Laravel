<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Sentinel;
use Closure;
use Illuminate\Http\Response;

class CheckRole
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
        if ($this->sentinel->check()) {
            if (request('user_id')) {
                $user = $this->sentinel->getUser()->childAccounts()->findOrFail(request('user_id'));
            } else {
                $user = $this->sentinel->getUser();
            }

            if (in_array($user->getAccountType(), $roles)) {
                return $next($request);
            }
        }

        return abort(404);
    }

}