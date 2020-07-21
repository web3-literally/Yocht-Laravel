<?php

namespace App\Rules;

use App\CrewMember;
use App\Employee;
use App\Helpers\RelatedProfile;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class AssignedTo implements Rule
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Create a new rule instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value) {
            $relatedMember = RelatedProfile::currentRelatedMember();
            if ($relatedMember->parent->id == $value) {
                return true;
            }
            if ($relatedMember->isBusinessAccount()) {
                return Employee::join('businesses_employees', 'users.id', '=', 'businesses_employees.user_id')
                    ->where('businesses_employees.business_id', $relatedMember->profile->id)
                    ->where('businesses_employees.user_id', $value)
                    ->exists();
            }
            if ($relatedMember->isVesselAccount()) {
                return CrewMember::join('vessels_crew', 'users.id', '=', 'vessels_crew.user_id')
                    ->where('vessels_crew.vessel_id', $relatedMember->profile->id)
                    ->where('vessels_crew.user_id', $value)
                    ->exists();
            }
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
