<?php

namespace App\Http\Requests;

use App\Rules\Address;
use App\Rules\Link;
use Sentinel;


class ProfileContactRequest extends AbstractProfileRequest
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
            'profile.company_name' => 'company name',
            'profile.company_email' => 'company email',
            'profile.company_country' => 'company country',
            'profile.company_state' => 'company state',
            'profile.company_city' => 'company city',
            'profile.company_address' => 'company address',

            'profile.link_website' => 'website link',
            'profile.link_blog' => 'blog link',
            'profile.link_youtube' => 'youtube link',
            'profile.link_pinterest' => 'pinterest link',
            'profile.link_twitter' => 'twitter link',
            'profile.link_facebook' => 'facebook link',
            'profile.link_linkedin' => 'linkedin link',
            'profile.link_google_plus' => 'google plus link',
            'profile.link_instagram' => 'instagram link',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = $this->getUser()->id;

        $rules = [
            'first_name' => 'required|alpha|min:3|max:191',
            'last_name' => 'required|alpha|min:3|max:191',
            'email' => 'required|unique:users,email,' . $user_id,
            'phone' => 'required|max:191',
            'address' => ['nullable', resolve(Address::class)],

            'profile.company_name' => 'nullable|min:3|max:191',
            'profile.company_email' => 'nullable|email',
            'profile.company_country' => 'nullable|max:191',
            'profile.company_state' => 'nullable|max:191',
            'profile.company_city' => 'nullable|max:191',
            'profile.company_address' => 'nullable|max:191',

            'profile.experience' => 'nullable|numeric|min:1|max:50',

            'profile.link_website' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_blog' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_youtube' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_pinterest' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_twitter' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_facebook' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_linkedin' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_google_plus' => ['nullable', resolve(Link::class), 'max:191'],
            'profile.link_instagram' => ['nullable', resolve(Link::class), 'max:191'],
        ];

        return $rules;
    }
}
