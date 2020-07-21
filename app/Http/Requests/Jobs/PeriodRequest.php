<?php

namespace App\Http\Requests\Jobs;

use App\Helpers\RelatedProfile;
use App\Repositories\PeriodsRepository;
use App\Rules\MyVessel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Jobs\Period;

/**
 * Class PeriodRequest
 * @package App\Http\Requests\Jobs
 */
class PeriodRequest extends FormRequest
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
            'period.period_id' => 'period',
            'period.name' => 'period name',
            'period.shipyard_name' => 'shipyard name',
            'period.month' => 'month',
            'period.year' => 'year',
            'period.period_type' => 'shipyard period',
            'period.from' => 'from',
            'period.to' => 'to',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        $periods = \App\Models\Jobs\Period::getPeriodTypes();
        $period = resolve(PeriodsRepository::class)->getOpenedPeriod($relatedMember->profile->id);
        if ($period) {
            $periods += $period;
        }

        $rules = [
            'period.period_id' => 'nullable|'.Rule::in(array_keys($periods)),
            'period.name' => 'required_if:period.period_id,yard_period,emergancy_yard_period,refit_period|nullable|min:3|max:191',
            'period.shipyard_name' => 'required_if:period.period_id,yard_period,emergancy_yard_period,refit_period|nullable|max:191',
            'period.month' => 'required_if:period.period_id,yard_period,emergancy_yard_period,refit_period|nullable|between:1,12',
            'period.year' => 'required_if:period.period_id,yard_period,emergancy_yard_period,refit_period|nullable|numeric|min:1900',
            'period.from' => 'required_with:period.to|nullable|date',
            'period.to' => 'required_with:period.from|nullable|date|after_or_equal:period.from',
        ];

        return $rules;
    }
}
