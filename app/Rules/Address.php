<?php

namespace App\Rules;

use App\Helpers\Geocoding;
use Illuminate\Contracts\Validation\Rule;

class Address implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $extra = [];
        if ($city = request('city')) {
            $extra[] = $city;
        }
        if ($state = request('user_state')) {
            $extra[] = $state;
        }
        if ($country = request('country')) {
            $extra[] = $country;
        }

        $address = trim($value . ($extra ? ' ' . implode(' ', $extra) : ''));
        if (empty($address)) {
            return true;
        }

        $response = Geocoding::latlngLookup($address);

        if ($response && $response->status === 'OK') {
            if ($response->results) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Unknown address.';
    }
}
