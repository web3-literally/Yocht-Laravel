<?php

namespace App\Http\Requests\Services;

use App\Models\Services\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
            'category_id' => 'category'
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
            'title' => 'required|max:191',
            'category_id' => 'required|exists:services_categories,id',
            'type' => 'nullable|' . Rule::in(array_keys(Service::GROUPS)),
            //'description' => 'required',
            'image' => 'nullable|image'
        ];
    }
}
