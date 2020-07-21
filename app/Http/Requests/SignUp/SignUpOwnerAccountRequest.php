<?php

namespace App\Http\Requests\SignUp;

use App\Rules\Address;
use App\Rules\GoogleReCaptchaV2ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SignUpOwnerAccountRequest
 * @package App\Http\Requests\SignUp
 */
class SignUpOwnerAccountRequest extends FormRequest
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
            'email' => 'e-mail',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
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
            'first_name' => 'required|alpha|min:3|max:191',
            'last_name' => 'required|alpha|min:3|max:191',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:191|phone:AUTO,US',
            'city' => 'required|min:3|max:191',
            'address' => ['required', 'min:3', 'max:191', resolve(Address::class)],
            'g-recaptcha-response' => [resolve(GoogleReCaptchaV2ValidationRule::class)]
        ];
    }
}

