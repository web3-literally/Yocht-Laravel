<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Sentinel;

abstract class AbstractProfileRequest extends FormRequest
{
    /**
     * @return User|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUser()
    {
        if (request('user_id')) {
            return User::childAccounts()->findOrFail(request('user_id'));
        }

        return Sentinel::getUser();
    }
}
