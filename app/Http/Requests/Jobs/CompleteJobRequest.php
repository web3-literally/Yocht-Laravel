<?php

namespace App\Http\Requests\Jobs;

use App\Http\Controllers\Traits\ImageUploadTrait;
use App\Models\Jobs\Job;
use App\Models\Vessels\Vessel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CompleteJobRequest
 * @package App\Http\Requests\Jobs
 */
class CompleteJobRequest extends FormRequest
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
            'message' => mb_strtolower(trans('reviews.review'))
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
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'required_with:message|max:191',
            'message' => 'max:2500',
        ];
    }
}
