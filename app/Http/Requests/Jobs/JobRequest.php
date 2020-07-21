<?php

namespace App\Http\Requests\Jobs;

use App\Models\Jobs\Job;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Vessels\Vessel;
use App\Role;
use App\Rules\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class JobRequest
 * @package App\Http\Requests\Jobs
 */
class JobRequest extends FormRequest
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
            'members' => 'contractors',
            'location_id' => 'location',
            'vessel_id' => 'vessel',
            'content' => 'description',
            'starts_at' => 'employment start date',
            'p_o_number' => 'p/o number'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'visibility' => 'required|' . Rule::in(Job::VISIBILITY),
            'title' => 'required|unique:jobs' . ($this->route('id') ? ',title,' . $this->route('id') : '') . '|min:3|max:191',
            'content' => 'required',
            'p_o_number' => 'nullable|max:191',
            'warranty' => 'nullable|boolean',
            //'address' => ['required_without:vessel_id', 'nullable', 'min:3', 'max:191', resolve(Address::class)], // Jobs will always use vessel address, because vessel required
            'image' => 'nullable|image',
            'categories' => 'nullable',
            'categories.*' => 'exists:services_categories,id',
            'services' => 'nullable',
            'services.*' => 'exists:services,id',
        ];

        return $rules;
    }
}
