<?php

namespace App\Http\Requests\Businesses;

use Illuminate\Foundation\Http\FormRequest;

class BusinessPhotoRequest extends FormRequest
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
        $rules = [
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:min_width=1024,min_height=768',
        ];

        return $rules;
    }
}
