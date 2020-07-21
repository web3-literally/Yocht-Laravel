<?php

namespace App\Http\Requests\SignUp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SignUpRequest
 * @package App\Http\Requests\SignUp
 */
class SignUpRequest extends FormRequest
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
            'dob' => 'birthday',
            'phone' => 'telephone',
            'email' => 'e-mail',
            'photo' => 'profile photo',
            'account_type' => 'account type'
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
            'first_name' => 'required|alpha|min:3|max:191',
            'last_name' => 'required|alpha|min:3|max:191',
            'dob' => 'required|date_format:Y-m-d|before_or_equal:' . date('Y', strtotime('-1 year')) . '-12-31',
            'phone' => 'required|max:191|phone:AUTO,US',
            'email' => 'required|email|unique:users,email',
            'photo' => 'required|mimes:jpg,jpeg,png,gif|max:10000',
            'account_type' => 'required|' . Rule::in(['yacht-owner', 'marine-contractor', 'free']),
        ];
    }
}

