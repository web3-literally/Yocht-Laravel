<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Sentinel;

class IsOwnerMember implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value) {
            return (bool)User::members(['owner'])->where('users.id', '!=', Sentinel::getUser()->getUserId())->find($value);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Member account ID is invalid or does not exists.';
    }
}
