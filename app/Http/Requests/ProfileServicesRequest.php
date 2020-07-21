<?php

namespace App\Http\Requests;

use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Role;
use Illuminate\Validation\Rule;

class ProfileServicesRequest extends AbstractProfileRequest
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
        return parent::attributes();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $role = Role::where('slug', $this->getUser()->getAccountType())->firstOrFail();
        $categories = ServiceCategory::where('provided_by', $role->id)->pluck('id')->all();
        $services = Service::whereIn('category_id', $categories)->pluck('id')->all();

        return [
            'services' => 'required',
            'services.*' => Rule::in($services),
        ];
    }
}
