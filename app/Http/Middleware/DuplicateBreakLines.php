<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * Class DuplicateBreakLines
 * @package App\Http\Middleware
 */
class DuplicateBreakLines extends TransformsRequest
{
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed|string
     */
    protected function transform($key, $value)
    {
        if (in_array($key, ['message'], true)) {
            return preg_replace("/[\r\n]+/", "\n", $value);
        }
        return $value;
    }
}