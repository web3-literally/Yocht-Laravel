<?php

namespace App\Repositories;

use App\ExtraOffer;
use App\Models\Vessels\Vessel;
use InfyOm\Generator\Common\BaseRepository;

class ExtraCrewOfferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExtraOffer::class;
    }

    /**
     * @param int $vesselId
     * @return int
     */
    public function getVesselTeamSlotsCount(int $vesselId)
    {
        $extra = ExtraOffer::my()->extraTeamMember()->where('status', '!=', 'fail')->where('vessel_id', $vesselId)->count();
        return config('billing.vessel.free_crew_members_count') + $extra;
    }

    /**
     * @param Vessel $vessel
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function chargeForExtraCrewMember(Vessel $vessel, $custom = [])
    {
        $owner = $vessel->owner;

        $now = time();

        /** @var ExtraOffer $offer */
        $offer = new ExtraOffer();
        $offer->user_id = $owner->id;
        $offer->vessel_id = $vessel->id;
        $offer->name = 'ExtraTeamMember';
        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = $custom;
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra crew team fee' . (isset($custom['for_member_id']) ? ' for Member #' . $custom['for_member_id'] : ''), config('billing.vessel.extra_crew_team_cost'));
    }

    /**
     * @param ExtraOffer $offer
     * @return mixed
     * @throws \Throwable
     */
    public function renewExtraCrewMember(ExtraOffer $offer)
    {
        $owner = $offer->user;

        $now = time();

        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->paused_at = null;
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = [];
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra crew team fee', config('billing.vessel.extra_crew_team_cost'));
    }

    /**
     * @param Vessel $vessel
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function prolongExtraCrewMember(Vessel $vessel, $custom = [])
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->extraTeamMember()->pause()->where('vessel_id', $vessel->id)->first();
        if (!$offer) {
            return null;
        }

        $offer->custom = $custom;

        return $offer->setActive();
    }

    /**
     * @param Vessel $vessel
     * @return ExtraOffer|null
     * @throws \Throwable
     */
    public function pauseExtraCrewMember(Vessel $vessel)
    {
        /** @var ExtraOffer $offer */
        $vesselTeamSlotsCount = $this->getVesselTeamSlotsCount($vessel->id);
        if ($vessel->crew->count() >= $vesselTeamSlotsCount) {
            return null;
        }
        $offer = ExtraOffer::my()->extraTeamMember()->active()->where('vessel_id', $vessel->id)->first();
        if (!$offer) {
            return null;
        }

        return $offer->setPause();
    }
}
