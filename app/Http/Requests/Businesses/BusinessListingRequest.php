<?php

namespace App\Http\Requests\Businesses;

use App\Models\Business\Business;
use App\Rules\Link;
use Igaster\LaravelCities\Geo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessListingRequest extends FormRequest
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
            'established_year' => 'company established year',
            'company_country' => 'company country',
            'company_phone' => 'company phone',
            'company_phone_alt' => 'company alt phone',
            'hours_of_operation' => 'hours of operation',
            'brochure_file' => 'brochure file',

            'staff.*.type' => 'staff type',
            'staff.*.name' => 'staff name',
            'staff.*.phone' => 'staff phone',
            'staff.*.email' => 'staff email',

            'owners.*.email' => 'owner email',
            'owners.*.first_name' => 'owner first name',
            'owners.*.last_name' => 'owner last name',
            'owners.*.phone' => 'owner phone',
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
            'staff' => 'nullable',
            'staff.*.type' => 'required|' . Rule::in('manager', 'salesman'),
            'staff.*.name' => 'required|min:3|max:191',
            'staff.*.phone' => 'nullable|max:191|phone:AUTO,US',
            'staff.*.email' => 'nullable|email',

            'owners' => 'nullable',
            'owners.*.email' => 'required|email',
            'owners.*.first_name' => 'required|min:3|max:191',
            'owners.*.last_name' => 'required|min:3|max:191',
            'owners.*.phone' => 'nullable|distinct|max:191|phone:AUTO,US',
        ];

        if ($business->business_type == 'marinas_shipyards') {
            $rules['established_year'] = 'required|numeric|min:1900|max:' . date('Y');
            $rules['company_country'] = 'required|' . Rule::in($countries);
            $rules['hours_of_operation'] = 'required|max:191';
            $rules['company_website'] = ['nullable', resolve(Link::class), 'max:191'];
        } elseif ($business->business_type == 'marine' || $business->business_type == 'land_services') {
            $rules['established_year'] = 'required|numeric|min:1900|max:' . date('Y');
            $rules['company_country'] = 'required|' . Rule::in($countries);
            $rules['hours_of_operation'] = 'required|max:191';
            $rules['company_website'] = ['nullable', resolve(Link::class), 'max:191'];
        }

        $rules['company_phone'] = 'required|max:191|phone:AUTO,US';
        $rules['company_phone_alt'] = 'nullable|max:191|phone:AUTO,US';
        $rules['brochure_file'] = 'nullable|mimes:doc,docx,odt,pdf|max:10000';

        return $rules;
    }
}
