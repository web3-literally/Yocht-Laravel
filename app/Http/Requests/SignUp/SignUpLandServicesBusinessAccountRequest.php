<?php

namespace App\Http\Requests\SignUp;

use App\Rules\Address;
use App\Rules\Link;
use Igaster\LaravelCities\Geo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SignUpLandServicesBusinessAccountRequest
 * @package App\Http\Requests\SignUp
 */
class SignUpLandServicesBusinessAccountRequest extends FormRequest
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
            'business.*.company_name' => 'company name',
            'business.*.company_email' => 'company email',
            'business.*.company_city' => 'company city',
            'business.*.company_address' => 'company address',
            'business.*.established_year' => 'company established year',
            'business.*.company_country' => 'company country',
            'business.*.company_phone' => 'company phone',
            'business.*.company_phone_alt' => 'company alt phone',
            'business.*.hours_of_operation' => 'hours of operation',

            'business.*.staff.*.type' => 'staff type',
            'business.*.staff.*.name' => 'staff name',
            'business.*.staff.*.phone' => 'staff phone',
            'business.*.staff.*.email' => 'staff email',

            'business.*.owners.*.email' => 'owner email',
            'business.*.owners.*.first_name' => 'owner first name',
            'business.*.owners.*.last_name' => 'owner last name',
            'business.*.owners.*.phone' => 'owner phone',

            'business.*.categories' => 'specialization category',
            'business.*.categories.*' => 'specialization category',
            'business.*.services' => 'specialization',
            'business.*.services.*' => 'specialization',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $countries = Geo::getCountries()->pluck('country')->all();

        return [
            'business' => 'required',
            'business.*.company_name' => 'required|min:3|max:191',
            'business.*.company_email' => 'nullable|email|unique:users,email',
            'business.*.established_year' => 'required|numeric|min:1900|max:' . date('Y'),
            'business.*.company_country' => 'required|' . Rule::in($countries),
            'business.*.company_city' => 'nullable|min:3|max:191',
            'business.*.company_phone' => 'required|max:191|phone:AUTO,US',
            'business.*.company_phone_alt' => 'nullable|max:191|phone:AUTO,US',
            'business.*.hours_of_operation' => 'required|max:191',
            'business.*.company_address' => ['required', 'min:3', 'max:191', resolve(Address::class)],
            'business.*.company_website' => ['nullable', resolve(Link::class), 'max:191'],

            'business.*.staff' => 'nullable',
            'business.*.staff.*.type' => 'required|' . Rule::in('manager', 'salesman'),
            'business.*.staff.*.name' => 'required|min:3|max:191',
            'business.*.staff.*.phone' => 'nullable|max:191|phone:AUTO,US',
            'business.*.staff.*.email' => 'nullable|email',

            'business.*.owners' => 'nullable',
            'business.*.owners.*.email' => 'required|email',
            'business.*.owners.*.first_name' => 'required|min:3|max:191',
            'business.*.owners.*.last_name' => 'required|min:3|max:191',
            'business.*.owners.*.phone' => 'nullable|distinct|max:191|phone:AUTO,US',

            'business.*.categories' => 'required',
            'business.*.categories.*' => 'exists:services_categories,id',
            'business.*.services' => 'nullable',
            'business.*.services.*' => 'exists:services,id'
        ];
    }
}

