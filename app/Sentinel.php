<?php

namespace App;

use Cartalyst\Sentinel\Users\UserInterface;

/**
 * Class Sentinel
 * @package App
 */
class Sentinel extends \Cartalyst\Sentinel\Sentinel
{
    /**
     * Persists a login for the given user.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @param  bool  $remember
     * @return \Cartalyst\Sentinel\Users\UserInterface|bool
     */
    public function login(UserInterface $user, $remember = false)
    {
        $method = $remember === true ? 'persistAndRemember' : 'persist';

        $this->persistences->{$method}($user);

        return $this->user = $user;
    }

    /**
     * Logs the current user out.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @param  bool  $everywhere
     * @return bool
     */
    public function logout(UserInterface $user = null, $everywhere = false)
    {
        $currentUser = $this->check();

        if ($user && $user !== $currentUser) {
            $this->persistences->flush($user, false);

            return true;
        }

        $user = $user ?: $currentUser;

        if ($user === false) {
            return true;
        }

        $method = $everywhere === true ? 'flush' : 'forget';

        $this->persistences->{$method}($user);

        $this->user = null;

        return $user;
    }
}