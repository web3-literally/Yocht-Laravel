<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use TimeHunter\LaravelGoogleReCaptchaV2\Facades\GoogleReCaptchaV2;

class GoogleReCaptchaV2ValidationRule extends \TimeHunter\LaravelGoogleReCaptchaV2\Validations\GoogleReCaptchaV2ValidationRule implements ImplicitRule
{
    protected $ip;

    protected $message;

    public function __construct()
    {
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!config('googlerecaptchav2.is_service_enabled')) {
            return true;
        }

        $response = GoogleReCaptchaV2::verifyResponse($value, app('request')->getClientIp());
        if (!$response->isSuccess() && empty($response->getErrorCodes())) {
            $this->message = 'Please, complete verification that you\'re not a robot';
        } else {
            $this->message = $response->getMessage();
        }

        return $response->isSuccess();
    }

    /**
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
