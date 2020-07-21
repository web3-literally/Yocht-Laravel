<?php

namespace App\Helpers;

use App\User;
use Sentinel;

/**
 * Class RelatedProfile
 * @package App\Helpers
 */
class RelatedProfile
{
    /**
     * @return User|null
     */
    static public function currentRelatedMember()
    {
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->isMemberMarineAccount() || $user->isMemberMarinasShipyards() || $user->isLandServicesAccount()) {
            $model = self::currentBusinessMember();
        }

        if ($user->isMemberOwnerAccount() || $user->isCaptainAccount() || $user->isCrewAccount()) {
            $model = self::currentVesselMember();
        }

        return $model ?? null;
    }

    static public function currentBusinessMember()
    {
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->isMemberMarineAccount() || $user->isMemberMarinasShipyards() || $user->isLandServicesAccount()) {
            $model = Business::currentBusiness();
        }

        return (!isset($model) || is_null($model)) ? null : $model->user;
    }

    static public function currentVesselMember()
    {
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->isMemberOwnerAccount() || $user->isCaptainAccount() || $user->isCrewAccount()) {
            $model = Vessel::currentVessel();
        }

        return (!isset($model) || is_null($model)) ? null : $model->user;
    }
}