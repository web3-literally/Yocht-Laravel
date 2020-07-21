<?php

namespace App\Http\Requests;

use App\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
        $plans = Plan::active()->forCurrentMember()->pluck('id')->all();
        return [
            'plan' => 'required|'. Rule::in($plans),
            'payment_method_nonce' => 'required',
        ];
    }

}
