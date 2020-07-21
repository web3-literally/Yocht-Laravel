<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MenuRequest
 * @package App\Http\Requests\Menu
 */
class MenuRequest extends FormRequest
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
            'label' => 'required|unique:menus' . ($this->route('id') ? ',label,' . $this->route('id') : '') . '|min:3|max:191',
            'type' => 'required|' . Rule::in(\App\Models\Menu::MENU_TYPES),
        ];
    }
}
