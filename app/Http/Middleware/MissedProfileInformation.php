<?php

namespace App\Http\Middleware;

use App\User;
use Cartalyst\Sentinel\Sentinel;
use Closure;

/**
 * Class MissedProfileInformation
 *
 * @package App\Http\Middleware
 */
class MissedProfileInformation
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->sentinel->check()) {
            /** @var User $user */
            $user = $this->sentinel->getUser();
            if ($user->isMemberOwnerAccount()) {
                if (empty($user->full_name)
                    || empty($user->phone)
                    || empty($user->country)
                    || empty($user->city)
                    || empty($user->address)
                ) {
                    return redirect()->route('complete.profile.index');
                }
            }
        }

        return $next($request);
    }

}