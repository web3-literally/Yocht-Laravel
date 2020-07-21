<?php

namespace App\Http\Requests\Classifieds;

use App\Models\Classifieds\Classifieds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Address;

class DashboardClassifiedsRequest extends FormRequest
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
        $attributes = [
            'category_id' => 'category',
            'manufacturer_id' => 'manufacturer',
        ];

        $type = $this->get('type');
        if ($type == 'part') {
            $attributes['title'] = 'name of part';
        }
        if ($type == 'accessory') {
            $attributes['title'] = 'title of product';
        }

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($id = $this->route('id')) {
            $model = Classifieds::findOrFail($id);
            $type = $model->type;
            $category = $model->category_id;
        } else {
            $type = $this->get('type');
            $category = $this->get('category_id');
        }

        $rules = [
            'vessel_id' => 'nullable|numeric|exists:vessels,user_id',
            'refresh_email' => 'nullable|email',
            'title' => 'required|min:3|max:191',
            'category_id' => [
                'required',
                Rule::exists('classifieds_categories', 'id')->where('type', $type)
            ],
            'manufacturer_id' => 'required',
            'address' => ['nullable', resolve(Address::class)],
            'price' => 'required|numeric|min:0|max:200000000',
            'state' => 'required|' . Rule::in(array_keys(Classifieds::getStates())),
            'description' => 'required',
            'year' => 'nullable|numeric|min:1900',
            'length' => 'nullable|numeric|min:1',
            'part_no' => 'nullable|string|max:191|regex:/(^([a-zA-Z0-9]+)$)/u',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:min_width=1024,min_height=768'
        ];

        if (!$this->route('id')) {
            // New Classifieds
            $rules['type'] = 'required|' . Rule::in(array_keys(Classifieds::getTypes()));
        }

        $manufacturer = $this->get('manufacturer_id');
        if (is_numeric($manufacturer)) {
            $rules['manufacturer_id'] = ['required', Rule::exists('classifieds_manufacturers', 'id')->where('type', $type)];
        } else {
            $rules['manufacturer_id'] = 'required|min:1|max:191';
        }

        return $rules;
    }
}
