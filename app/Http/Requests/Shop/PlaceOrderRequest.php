<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop\Product;

class PlaceOrderRequest extends FormRequest
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
            'shipping_address' => 'required|min:3|max:191',
            'shipping_country' => 'required|exists:countries,id',
            'shipping_state' => 'nullable|min:3|max:191',
            'shipping_city' => 'required|min:3|max:191',
            'shipping_postcode' => 'required|max:10',
        ];
    }
}
