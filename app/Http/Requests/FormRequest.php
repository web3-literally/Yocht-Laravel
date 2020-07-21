<?php

namespace App\Http\Requests;

/**
 * Class FormRequest
 * @package App\Http\Requests
 */
class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => 'e-mail',
            'user_state' => 'state',

            'profile.alt_phone' => 'phone',
            'profile.personal_quote' => 'personal quote',
            'profile.established_year' => 'established year',
            'profile.hours_of_operation' => 'hour open',
            'profile.link_website' => 'web site',

            'profile.company_name' => 'company name',
            'profile.company_email' => 'company email',
            'profile.company_country' => 'company country',
            'profile.company_state' => 'company state',
            'profile.company_city' => 'company city',
            'profile.company_address' => 'company address',

            'profile.vhf_channel' => 'vhf channel',
            'profile.number_of_ships' => 'number of slips',
            'profile.min_depth' => 'min depth',
            'profile.max_depth' => 'max depth',

            'services' => 'services',
            'services.*' => 'services',
        ];
    }
}