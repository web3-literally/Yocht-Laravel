<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * Class StripTags
 * @package App\Http\Middleware
 */
class StripTags extends TransformsRequest
{
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed|string
     */
    protected function transform($key, $value)
    {
        if (in_array($key, ['message'], true)) {
            return strip_tags($value);
        }
        return $value;
    }
}