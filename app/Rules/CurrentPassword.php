<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Sentinel;

/**
 * Class CurrentPassword
 * @package App\Rules
 */
class CurrentPassword implements Rule
{
    /**
     * The hasher instance.
     *
     * @var \Cartalyst\Sentinel\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->hasher = Sentinel::getHasher();
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
        $user = Sentinel::getUser();

        return $this->hasher->check($value, $user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The current password isn\'t valid.';
    }
}
