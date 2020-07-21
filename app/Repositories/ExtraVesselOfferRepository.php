<?php

namespace App\Repositories;

use App\ExtraOffer;
use App\Helpers\Owner;
use App\Models\Vessels\Vessel;
use InfyOm\Generator\Common\BaseRepository;

class ExtraVesselOfferRepository extends BaseRepository
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
     * @return int
     */
    public function getVesselCount()
    {
        $owner = Owner::currentOwner();

        return Vessel::where('owner_id', $owner->getUserId())->where('type', 'vessel')->count();
    }

    /**
     * @return int
     */
    public function getVesselSlotsCount()
    {
        $extra = ExtraOffer::my()->extraVessel()->where('status', '!=', 'fail')->count();
        return config('billing.vessel.free_vessels_count') + $extra;
    }

    /**
     * @return int
     */
    public function getTenderCount()
    {
        $owner = Owner::currentOwner();

        return Vessel::where('owner_id', $owner->getUserId())->where('type', 'tender')->count();
    }

    /**
     * @return int
     */
    public function getTenderSlotsCount()
    {
        $extra = ExtraOffer::my()->extraTender()->where('status', '!=', 'fail')->count();
        return config('billing.vessel.free_tenders_count') + $extra;
    }

    /**
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function chargeForExtraVessel($custom = [])
    {
        $owner = Owner::currentOwner();

        $now = time();

        /** @var ExtraOffer $offer */
        $offer = new ExtraOffer();
        $offer->user_id = $owner->id;
        $offer->name = 'ExtraVessel';
        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = $custom;
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra vessel fee' . (isset($custom['for_boat_id']) ? ' for Vessel #' . $custom['for_boat_id'] : ''), config('billing.vessel.extra_vessel_cost'));
    }

    /**
     * @param ExtraOffer $offer
     * @return mixed
     * @throws \Throwable
     */
    public function renewExtraVessel(ExtraOffer $offer)
    {
        $owner = $offer->user;

        $now = time();

        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->paused_at = null;
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = [];
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra vessel fee', config('billing.vessel.extra_vessel_cost'));
    }

    /**
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function prolongExtraVessel($custom = [])
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->extraVessel()->pause()->first();
        if (!$offer) {
            return null;
        }

        $offer->custom = $custom;

        return $offer->setActive();
    }

    /**
     * @return ExtraOffer|null
     * @throws \Throwable
     */
    public function pauseExtraVessel()
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->extraVessel()->active()->first();
        if (!$offer) {
            return null;
        }

        return $offer->setPause();
    }

    /**
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function chargeForExtraTender($custom = [])
    {
        $owner = Owner::currentOwner();

        $now = time();

        /** @var ExtraOffer $offer */
        $offer = new ExtraOffer();
        $offer->user_id = $owner->id;
        $offer->name = 'ExtraTender';
        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = $custom;
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra tender fee' . (isset($custom['for_boat_id']) ? ' for Tender #' . $custom['for_boat_id'] : ''), config('billing.vessel.extra_tender_cost'));
    }

    /**
     * @param ExtraOffer $offer
     * @return mixed
     * @throws \Throwable
     */
    public function renewExtraTender(ExtraOffer $offer)
    {
        $owner = $offer->user;

        $now = time();

        $offer->status = 'active';
        $offer->started_at = date('Y-m-d H:i:s', $now);
        $offer->paused_at = null;
        $offer->finished_at = date('Y-m-d H:i:s', strtotime('+1 month', $now));
        $offer->custom = [];
        $offer->saveOrFail();

        return $owner->invoiceFor('Extra tender fee', config('billing.vessel.extra_tender_cost'));
    }

    /**
     * @param array $custom
     * @return mixed
     * @throws \Throwable
     */
    public function prolongExtraTender($custom = [])
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->extraTender()->pause()->first();
        if (!$offer) {
            return null;
        }

        $offer->custom = $custom;

        return $offer->setActive();
    }

    /**
     * @return ExtraOffer|null
     * @throws \Throwable
     */
    public function pauseExtraTender()
    {
        /** @var ExtraOffer $offer */
        $offer = ExtraOffer::my()->extraTender()->active()->first();
        if (!$offer) {
            return null;
        }

        return $offer->setPause();
    }
}
