<?php

namespace App\Helpers;

use App\User;
use Sentinel;

/**
 * Class Owner
 * @package App\Helpers
 */
class Owner
{
    /**
     * @return \App\User
     */
    static public function currentOwner()
    {
        /** @var User $owner */
        if (Sentinel::getUser()->isCaptainAccount()) {
            $owner = Sentinel::getUser()->parent;
        } else {
            $owner = Sentinel::getUser();
        }

        return $owner;
    }
}