<?php

namespace App\Http\Requests;

use App\CrewMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sentinel;

class CreateMemberRequest extends FormRequest
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
            'position_id' => 'position',
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
        $roles = CrewMember::CREW_ROLES;
        if (Sentinel::getUser()->isCaptainAccount()) {
            // Captain can't assign a captain
            if (($key = array_search('captain', $roles)) !== false) {
                unset($roles[$key]);
            }
            $roles = ['crew'];
        }

        return [
            'role' => 'required|' . Rule::in($roles),
            'position_id' => 'required_if:role,crew|nullable|exists:positions,id',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:3|max:191',
            'last_name' => 'required|min:3|max:191',
            'phone' => 'required|max:191|phone:AUTO,US',
            'experience' => 'nullable|numeric|min:1|max:50',
            'country' => 'nullable|min:2|max:2',
            'pic' => 'nullable|image',
            'cv' => 'nullable|file|mimes:pdf,doc,docx,odt|max:10000',
            'certificates' => 'nullable',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,bmp,png|max:10000',
        ];
    }

}
