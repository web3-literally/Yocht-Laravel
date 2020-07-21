<?php

namespace App\Http\Requests\SignUp;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPasswordRequest extends FormRequest
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
            'user_id' => 'required',
            'activation_code' => 'required',
            'new_password' => 'required|between:3,32',
            'confirm_password' => 'required|same:new_password'
        ];
    }
}
