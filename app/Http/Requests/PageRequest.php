<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class PageRequest
 * @package App\Http\Requests
 */
class PageRequest extends FormRequest
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
            'title' => 'required|unique:pages' . ($this->route('page') ? ',title,' . $this->route('page')->id : '') . '|min:3|max:191',
            'slug' => 'unique:pages' . ($this->route('page') ? ',slug,' . $this->route('page')->id : ''),
            'content' => 'required',
            'layout' => 'required|' . Rule::in(array_keys(\App\Helpers\Pages::getPageLayouts())),
        ];
    }
}
