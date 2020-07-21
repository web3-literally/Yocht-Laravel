<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'current_location',
        'current_location_city',
        'current_location_country',
        'current_location_lat',
        'current_location_lng',

        'job_visibility',
        'selected_members',

        'tasks_additional_columns'
    ];
}
