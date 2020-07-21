<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Sentinel;

class RelatedMemberRequired
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
        /** @var User $user */
        $user = Sentinel::getUser();

        if (Sentinel::check()) {
            if ($memberId = request('related_member_id')) {
                if ($user->isCaptainAccount() || $user->isCrewAccount()) {
                    /* Optimization required. Assigned boats should be requested once. */
                    $user = $user->asCrewMember();
                    $assignedVessels = $user->inCrewOf()->pluck('vessels.user_id')->all();
                    if (in_array($memberId, $assignedVessels)) {
                        return $next($request);
                    }
                } else {
                    if ($user->accounts->find($memberId)) {
                        return $next($request);
                    }
                }
            }
        }

        abort(404);
    }
}
