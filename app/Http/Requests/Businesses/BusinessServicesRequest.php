<?php

namespace App\Http\Requests\Businesses;

use Illuminate\Foundation\Http\FormRequest;

class BusinessServicesRequest extends FormRequest
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
            'categories' => 'specialization category',
            'categories.*' => 'specialization category',
            'services' => 'specialization',
            'services.*' => 'specialization',
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
            'categories' => 'required',
            'categories.*' => 'exists:services_categories,id',
            'services' => 'nullable',
            'services.*' => 'exists:services,id'
        ];

        return $rules;
    }
}
