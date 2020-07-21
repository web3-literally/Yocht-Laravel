<?php

namespace App\Models\ExtraOffers;

use App\Exceptions\PaymentException;
use App\ExtraOffer;

/**
 * Class ExtraTenderOffer
 * @package App
 */
class ExtraTenderOffer extends ExtraOffer
{
    /**
     * @throws \Exception
     */
    public function renew()
    {
        try {
            resolve('App\Repositories\ExtraVesselOfferRepository')->renewExtraTender($this);
        } catch (\Exception $e) {
            report($e);

            throw new PaymentException('Failed to charge additional fee. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }
}
