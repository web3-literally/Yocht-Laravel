<?php

namespace App\Http\Requests\Services;

use App\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $providedBy = Role::searchable()->orderBy('name', 'asc')->pluck('id')->all();

        return [
            'provided_by' => 'nullable|'. Rule::in($providedBy),
            'label' => 'required|min:3|max:191',
            'image' => 'nullable|image',
        ];
    }
}
