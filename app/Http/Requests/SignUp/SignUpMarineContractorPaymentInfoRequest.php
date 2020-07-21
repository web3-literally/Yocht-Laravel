<?php

namespace App\Http\Requests\SignUp;

use App\Plan;
use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SignUpMarineContractorPaymentInfoRequest
 * @package App\Http\Requests\SignUp
 */
class SignUpMarineContractorPaymentInfoRequest extends FormRequest
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
        $plans = Plan::active()->where('slug', 'like', "%marine%")->pluck('id')->all();

        return [
            'plan' => 'required|'. Rule::in($plans),
            'payment_method_nonce' => 'required',
        ];
    }
}

