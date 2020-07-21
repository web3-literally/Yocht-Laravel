<?php

namespace App\Models\ExtraOffers;

use App\Exceptions\PaymentException;
use App\ExtraOffer;

/**
 * Class ExtraVesselOffer
 * @package App
 */
class ExtraVesselOffer extends ExtraOffer
{
    /**
     * @throws \Exception
     */
    public function renew()
    {
        try {
            resolve('App\Repositories\ExtraVesselOfferRepository')->renewExtraVessel($this);
        } catch (\Exception $e) {
            report($e);

            throw new PaymentException('Failed to charge additional fee. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }
}
