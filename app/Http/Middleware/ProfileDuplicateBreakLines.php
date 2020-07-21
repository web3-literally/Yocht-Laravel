<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * Class ProfileDuplicateBreakLines
 * @package App\Http\Middleware
 */
class ProfileDuplicateBreakLines extends TransformsRequest
{
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed|string
     */
    protected function transform($key, $value)
    {
        if (in_array($key, ['hours_of_operation', 'accepted_forms_of_payments', 'credentials', 'honors_and_awards'], true)) {
            $value = preg_replace("/[\r\n]+/", "\n", $value);
        }
        return $value;
    }
}