<?php

namespace App\Http\Requests\Classifieds;

use App\Models\Classifieds\Classifieds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassifiedsRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|max:191',
            'category_id' => 'required|exists:classifieds_categories,id',
            'price' => 'required|numeric|min:0|max:200000000',
            'type' => 'required|'. Rule::in(array_keys(Classifieds::getTypes())),
            'state' => 'required|'. Rule::in(array_keys(Classifieds::getStates())),
            'description' => 'required',
            'year' => 'nullable|numeric|min:1900',
            'length' => 'nullable|numeric|min:1',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:min_width=1024,min_height=768'
        ];
    }
}
