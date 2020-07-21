<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop\Product;

class ProductRequest extends FormRequest
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
            'name' => 'required|unique:' . Product::getModel()->table . ($this->route('products') ? ',name,' . $this->route('products') : '') . '|min:3|max:191',
            'sku' => 'nullable|unique:' . Product::getModel()->table . ($this->route('products') ? ',sku,' . $this->route('products') : '') . '|max:191',
            'price' => 'required|numeric|min:1',
            'tax' => 'nullable|numeric|min:1',
            'url_key' => 'unique:' . Product::getModel()->table . ($this->route('products') ? ',url_key,' . $this->route('products') : '') . '|max:191',
        ];
    }
}
