<?php

namespace App\Http\Requests\Events;

use App\Http\Controllers\Traits\ImageUploadTrait;
use App\Models\Events\Event;
use App\Models\Jobs\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CategoryRequest
 * @package App\Http\Requests\Events
 */
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
        return [
            'label' => 'required|min:3|max:191',
            'image' => 'nullable|image',
        ];
    }
}
