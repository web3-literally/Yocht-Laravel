<?php

namespace App\Http\Requests;

use App\Rules\GoogleReCaptchaV2ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'contact-name' => 'required|min:3|max:191',
            'contact-email' => 'required|email',
            'contact-subject' => 'max:191',
            'message' => 'required|min:3|max:2500',

            'g-recaptcha-response' => [resolve(GoogleReCaptchaV2ValidationRule::class)]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'contact-name' => 'name',
            'contact-email' => 'email',
            'contact-subject' => 'subject'
        ];
    }
}
