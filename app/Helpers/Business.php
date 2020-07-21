<?php

namespace App\Helpers;

use App\Models\Business\Business as Model;
use App\User;
use Sentinel;

/**
 * Class Business
 * @package App\Helpers
 */
class Business
{
    /**
     * @return \App\Models\Business\Business
     */
    static public function currentBusiness()
    {
        $currentBusiness = null;
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->isMemberMarineAccount() || $user->isMemberMarinasShipyards() || $user->isLandServicesAccount()) {
            $relatedId = request()->route('related_member_id'); // related_member_id was validated by middleware
            if (empty($relatedId)) {
                $currentBusiness = $user->primaryBusiness;
            } else {
                $currentBusiness = Model::my()->where('user_id', $relatedId)->first();
            }
        }

        return $currentBusiness;
    }
}