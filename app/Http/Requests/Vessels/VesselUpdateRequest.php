<?php

namespace App\Http\Requests\Vessels;

use App\Helpers\Country;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vessels\Vessel;
use Illuminate\Validation\Rule;

class VesselUpdateRequest extends FormRequest
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
            'manufacturer_id' => 'build',
            'registered_port_id' => 'registration port',
            'registered_port' => 'registration port',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name_prefix' => Rule::in(array_keys(Vessel::getNamePrefixes())),
            'name' => 'required|min:2|max:191',
            'manufacturer_id' => 'required',
            'registered_port_id' => 'required|numeric',
            'propulsion' => 'required|' . Rule::in(array_keys(config('propulsion'))),
            'year' => 'required|numeric|min:1900|max:' . date('Y'),
            'length' => 'required|numeric|min:51',
            'fuel_type' => 'required|' . Rule::in(array_keys(Vessel::getFuelType())),
            'color' => 'required|min:2|max:191',
            'flag' => 'required|' . Rule::in(array_keys(Country::getAll())),
            'guest_capacity' => 'nullable|numeric|min:1',
            'crew_capacity' => 'nullable|numeric|min:1',
            'hull_type' => 'nullable|' . Rule::in(array_keys(Vessel::getHullTypes())),
            'max_speed' => 'nullable|numeric|min:1',
            'cruise_speed' => 'nullable|numeric|min:1',
            'vessel_type' => 'nullable|' . Rule::in(array_keys(config('vessel-types'))),
            'width' => 'nullable|numeric|min:1',
            'draft' => 'nullable|numeric|min:1',
            'fuel' => 'nullable|numeric|min:1',
            'fresh_water' => 'nullable|numeric|min:1',
            'black_water' => 'nullable|numeric|min:1',
            'grey_water' => 'nullable|numeric|min:1',
            'clean_oil' => 'nullable|numeric|min:1',
            'dirty_oil' => 'nullable|numeric|min:1',
            'gear_oil' => 'nullable|numeric|min:1',
            'description' => 'nullable',
            'imo' => 'nullable|max:191',
            'official' => 'nullable|max:191',
            'mmsi' => 'nullable|max:191',
            'call_sign' => 'nullable|max:191',
            'on' => 'nullable|max:191',
            'gross_tonnage' => 'nullable|max:191',
            'hull' => 'nullable|max:191',
            'number_of_engines' => 'nullable|max:191',
            'make_main_engines' => 'nullable|max:191',
            'engine_model' => 'nullable|max:191',
            'number_of_generators' => 'nullable|max:191',
            'make_main_generators' => 'nullable|max:191',
            'generator_model' => 'nullable|max:191',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:min_width=1024,min_height=768',
            'records' => 'nullable',
            'records.*' => 'file|mimes:mp4',
            'documents' => 'nullable',
            'documents.*' => 'file|mimes:pdf,doc,docx,odt',
        ];

        $manufacturer = $this->get('manufacturer_id');
        if (is_numeric($manufacturer)) {
            $rules['manufacturer_id'] = ['required', Rule::exists('classifieds_manufacturers', 'id')->where('type', 'boat')];
        } else {
            $rules['manufacturer_id'] = 'required|min:1|max:191';
        }

        return $rules;
    }
}
