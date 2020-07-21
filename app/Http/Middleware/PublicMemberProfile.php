<?php

namespace App\Http\Middleware;

use App\User;
use Cartalyst\Sentinel\Sentinel;
use Closure;
use Illuminate\Http\Response;

class PublicMemberProfile
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $type = $this->sentinel->getUser()->getAccountType();
        if ($id = $request->route('id')) {
            if ($member = User::members(['vessel', 'business'])->find($id)) {
                if ($member->profile->owner_id == $this->sentinel->getUser()->getUserId()) {
                    return $next($request);
                }
                if (in_array($type, ['owner', 'marine', 'marinas_shipyards', 'captain'])) {
                    return $next($request);
                }
            }
        }

        abort(404);
    }

}