<?php

namespace App\Http\Requests\Tasks;

use App\Models\Tasks\Task;
use App\Rules\AssignedTo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardTasksRequest extends FormRequest
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
        $attributes = [
            'description' => 'notes',
            'assigned_to_id' => 'assigned to',
            'due_date_at' => 'due date',
        ];

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'set_as' => ['required', Rule::in(array_keys(Task::getSetAsList()))],
            'priority' => ['required', Rule::in(array_keys(Task::getPriorityList()))],
            'title' => 'required|min:3|max:191',
            'description' => 'nullable',
            'assigned_to_id' => ['nullable', resolve(AssignedTo::class)],
            'due_date_at' => 'nullable|date',
        ];

        return $rules;
    }
}
