<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest {

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
		return [
            'title' => 'required|unique:blogs' . ($this->route('blog') ? ',title,' . $this->route('blog')->id : '') . '|min:3|max:191',
            'slug' => 'unique:blogs' . ($this->route('blog') ? ',slug,' . $this->route('blog')->id : ''),
            'status' => 'required|' . Rule::in(\App\Blog::STATUSES),
            'publish_on' => 'required|date',
            'content' => 'required|min:3',
			'blog_category_id' => 'required',
            'video' => 'nullable|file|mimes:mp4'
		];
	}

}
