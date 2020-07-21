<?php

namespace App\Http\Requests\Businesses;

use App\Models\Business\Business;
use App\Rules\Address;
use App\Rules\Link;
use Igaster\LaravelCities\Geo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'company_name' => 'company name',
            'company_email' => 'company email',
            'company_city' => 'company city',
            'company_address' => 'company address',
            'vhf_channel' => 'vhf channel',
            'number_of_ships' => 'number of slips',
            'map_file' => 'map file',
            'min_depth' => 'min depth',
            'max_depth' => 'max depth',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $business = Business::findOrFail($this->route('id'));

        $countries = Geo::getCountries()->pluck('country')->all();

        $rules = [
            'company_name' => 'required|min:3|max:191',
            'company_email' => 'nullable|email|unique:users,email',
            'company_city' => 'nullable|min:3|max:191',
            'company_address' => ['required', 'min:3', 'max:191', resolve(Address::class)],
        ];

        if ($business->business_type == 'marinas_shipyards') {
            $rules['vhf_channel'] = 'required|numeric|min:1';
            // Dock info
            $rules['number_of_ships'] = 'nullable|numeric|min:1';
            $rules['min_depth'] = 'nullable|numeric|min:1';
            $rules['max_depth'] = 'nullable|numeric|min:1';
        }

        if ($business->business_type == 'marinas_shipyards') {
            $rules['map_file'] = 'nullable|mimes:jpg,jpeg,png,gif|max:10000|dimensions:min_width=1024,min_height=768';
        }

        return $rules;
    }
}
