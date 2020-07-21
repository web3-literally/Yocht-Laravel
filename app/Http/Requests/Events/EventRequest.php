<?php

namespace App\Http\Requests\Events;

use App\Models\Events\Event;
use App\Rules\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sentinel;

/**
 * Class EventRequest
 * @package App\Http\Requests\Events
 */
class EventRequest extends FormRequest
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
            'category_id' => 'category',
            'content' => 'description',
            'starts_at' => 'start date',
            'starts_time' => 'start time',
            'ends_time' => 'end time',
            'ends_at' => 'end date',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $owner = Sentinel::getUser()->getUserId();

        return [
            'title' => 'required|min:3|max:191',
            'category_id' => 'required|exists:events_categories,id',
            'type' => 'required|' . Rule::in(array_keys(Event::getTypes())),
            'starts_at' => 'required|date|after:'.date('Y-m-d', strtotime('+1d')),
            'starts_time' => 'required',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'price' => 'nullable|numeric|min:1',
            'description' => 'nullable',
            'image_id' => ['nullable', Rule::exists('files', 'id')->where(function ($query) use ($owner) {
                $query->where('user_id', $owner);
            })],
            'address' => ['required', 'min:3', 'max:191', resolve(Address::class)]
        ];
    }
}
