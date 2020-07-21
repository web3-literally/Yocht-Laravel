<?php

namespace App\Rules;

use App\Helpers\Geocoding;
use App\Helpers\Owner;
use App\Models\Vessels\Vessel;
use Illuminate\Contracts\Validation\Rule;

class MyVessel implements Rule
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
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $owner = Owner::currentOwner();
        return (bool)Vessel::where('owner_id', $owner->getUserId())
            ->where('id', $value)
            ->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Unknown vessel.';
    }
}
