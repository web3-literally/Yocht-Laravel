<?php

namespace App\Http\Requests;

use App\Rules\Address;
use App\Rules\Link;
use Sentinel;


class PaymentMethodAdd extends AbstractProfileRequest
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
        $rules = [
            'payment_method_nonce' => 'required',
        ];

        return $rules;
    }
}
