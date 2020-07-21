<?php

namespace App\Http\Middleware;

use App\Models\Vessels\VesselsCrew;
use App\User;
use Closure;
use Sentinel;
use Illuminate\Support\Facades\URL;

class RelatedProfile
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
        if (Sentinel::check()) {
            URL::defaults(['related_member_id' => '-']);

            /** @var User $user */
            $user = Sentinel::getUser();

            if (in_array($user->getAccountType(), ['owner', 'marine', 'marinas_shipyards', 'land_services', 'captain', 'crew'])) {
                $memberId = request('related_member_id', '-');
                if ($memberId == '-') {
                    $related = \App\Helpers\RelatedProfile::currentRelatedMember();
                    $memberId = $related ? $related->id : '-';
                } else {
                    if ($user->isCaptainAccount() || $user->isCrewAccount()) {
                        /* Optimization required. Assigned boats should be requested once. */
                        $user = $user->asCrewMember();
                        $assignedVessels = $user->inCrewOf()->pluck('vessels.user_id')->all();
                        if (!in_array($memberId, $assignedVessels)) {
                            abort(404);
                        }
                    } else {
                        if (!$user->accounts->find($memberId)) {
                            abort(404);
                        }
                    }
                }
                URL::defaults(['related_member_id' => $memberId]);
            }
        }

        return $next($request);
    }
}
