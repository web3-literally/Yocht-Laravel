<?php

namespace App\Http\Requests;

use App\CrewMember;
use App\Employee;
use App\Helpers\Owner;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselsCrew;
use App\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sentinel;

class AssignEmployeeMemberRequest extends FormRequest
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
            'user_id' => 'member',
            'role' => 'position',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @throws \Exception
     * @return array
     */
    public function rules()
    {
        $roles = Employee::EMPLOYEE_ROLES;

        return [
            'role' => 'required|' . Rule::in($roles),
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:3|max:191',
            'last_name' => 'required|min:3|max:191',
            'phone' => 'required|max:191|phone:AUTO,US',
            'country' => 'nullable|max:191',
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif|max:10000',
        ];
    }

}
