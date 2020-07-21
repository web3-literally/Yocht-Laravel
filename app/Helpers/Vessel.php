<?php

namespace App\Helpers;

use App\Models\Vessels\Vessel as Model;
use App\Models\Vessels\VesselsCrew;
use App\User;
use Sentinel;

/**
 * Class Vessel
 * @package App\Helpers
 */
class Vessel
{
    /**
     * @return array
     */
    static public function colors()
    {
        return [
            'transparent' => '',
            '#00c0e4' => 'Sky Blue',
            '#5bd999' => 'Green',
            '#ffd772' => 'Yellow',
            '#cc687f' => 'Red',
            '#cb70d7' => 'Pink',
            '#7658f8' => 'Purple',
        ];
    }

    /**
     * @return \App\Models\Vessels\Vessel
     */
    static public function currentVessel()
    {
        /** @var User $user */
        $user = Sentinel::getUser();
        if ($user->isCaptainAccount() || $user->isCrewAccount()) {
            $link = VesselsCrew::where('user_id', $user->getUserId())->first();
            if (!$link) {
                return null;
            }
            $currentVessel = $link->vessel;
        } else {
            $relatedId = request()->route('related_member_id'); // related_member_id was validated by middleware
            if (empty($relatedId)) {
                $currentVessel = $user->primaryVessel;
            } else {
                $currentVessel = Model::my()->where('user_id', $relatedId)->first();
            }
        }

        return $currentVessel;
    }
}