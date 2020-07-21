<?php

namespace App\Http\Requests\Vessels;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vessels\Vessel;
use Illuminate\Validation\Rule;

class VesselLocationRequest extends FormRequest
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
        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'location_city.required' => 'Please, enter city or address',
            'location_country.required' => 'Please, enter city or address'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'required',
            'location_city' => 'required',
            'location_country' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ];
    }
}
