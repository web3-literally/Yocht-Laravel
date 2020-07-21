<?php

namespace App\Rules;

use App\Helpers\Geocoding;
use Illuminate\Contracts\Validation\Rule;

class CompanyAddress implements Rule
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
        if ($city = request('profile.company_city')) {
            $extra[] = $city;
        }
        if ($state = request('profile.company_state')) {
            $extra[] = $state;
        }
        if ($country = request('profile.company_country')) {
            $extra[] = $country;
        }

        $address = $value . ($extra ? ' ' . implode(' ', $extra) : '');

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
