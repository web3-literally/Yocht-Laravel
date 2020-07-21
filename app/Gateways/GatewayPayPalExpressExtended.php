<?php

namespace App\Gateways;

use Amsgames\LaravelShopGatewayPaypal\GatewayPayPalExpress;
use Amsgames\LaravelShop\Exceptions\CheckoutException;
use Amsgames\LaravelShop\Exceptions\GatewayException;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;
use Illuminate\Support\Facades\Config;

/**
 * Class GatewayPayPalExpressExtended
 * @package App\Gateways
 */
class GatewayPayPalExpressExtended extends GatewayPayPalExpress
{
    /**
     * Status code after placing order successfully.
     *
     * @var string
     */
    protected $statusCode = 'in_process';

    /**
     * Setups contexts for api calls.
     */
    private function setContext()
    {
        $this->apiContext = new ApiContext(new OAuthTokenCredential(
            Config::get('services.paypal.client_id'),
            Config::get('services.paypal.secret')
        ));

        if (!Config::get('services.paypal.sandbox'))
            $this->apiContext->setConfig(['mode' => 'live']);
    }

    /**
     * Called on callback.
     *
     * @param Order $order Order.
     * @param mixed $data  Request input from callback.
     *
     * @return bool
     * @throws GatewayException
     */
    public function onCallbackSuccess($order, $data = null)
    {
        $paymentId  = is_array($data) ? $data['paymentId'] : $data->paymentId;

        $payerId    = is_array($data) ? $data['PayerID'] : $data->PayerID;

        $this->statusCode = 'failed';

        $this->detail = sprintf('Payment failed. Ref: %s', $paymentId);

        // Begin paypal
        try {

            $this->setContext();

            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();

            $execution->setPayerId($payerId);

            $payment->execute($execution, $this->apiContext);

            $payment = Payment::get($paymentId, $this->apiContext);

            $this->statusCode = 'in_process';

            $this->transactionId = $payment->id;

            $this->detail = 'Success';

        } catch (PayPalConnectionException $e) {

            $response = json_decode($e->getData());

            throw new GatewayException(
                sprintf(
                    '%s: %s',
                    $response->name,
                    isset($response->message) ? $response->message : 'Paypal payment Failed.'
                ),
                1001,
                $e
            );

        } catch (\Exception $e) {

            throw new GatewayException(
                $e->getMessage(),
                1000,
                $e
            );

        }
    }
}