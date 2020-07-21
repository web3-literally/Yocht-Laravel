<?php

namespace App\Http\Requests\Jobs;

use App\Rules\JobForMember;
use Illuminate\Validation\Rule;

/**
 * Class JobWizardRequest
 * @package App\Http\Requests\Jobs
 */
class JobWizardRequest extends JobRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules['period_id'] = 'required_if:job_for,marine|nullable|exists:job_periods,id';
        $rules['members'] = 'required_if:visibility,private';
        $rules['members.*'] = resolve(JobForMember::class);

        return $rules;
    }
}
