<?php

namespace App\Http\Requests\Vessels;

use App\Helpers\Country;
use App\Rules\MyVessel;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vessels\Vessel;
use Illuminate\Validation\Rule;

class TenderRequest extends FormRequest
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
            'name' => 'label',
            'manufacturer_id' => 'build',
            'registered_port' => 'registration port',
            'make_main_engines' => 'make of engine'
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
            'name' => 'required|min:3|max:191',
            'parent_id' => ['nullable', resolve(MyVessel::class)],
            'manufacturer_id' => 'required',
            'frame' => 'nullable|' . Rule::in(['hard', 'inflatable']),
            'board' => 'nullable|' . Rule::in(['inboard', 'outboard']),
            'description' => 'nullable',
            'year' => 'required|numeric|min:1900|max:' . date('Y'),
            'number_of_engines' => 'nullable|numeric|min:1',
            'make_main_engines' => 'nullable|min:1|max:191',
            'hp_of_engine' => 'nullable|numeric|min:1',
            'registered_port' => 'nullable|' . Rule::in(array_keys(Country::getAll())),
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
