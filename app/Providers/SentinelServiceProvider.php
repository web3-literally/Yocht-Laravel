<?php

namespace App\Providers;

use App\Sentinel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SentinelServiceProvider
 * @package App\Providers
 */
class SentinelServiceProvider extends \Cartalyst\Sentinel\Laravel\SentinelServiceProvider
{
    /**
     * Registers sentinel.
     *
     * @return void
     */
    protected function registerSentinel()
    {
        $this->app->singleton('sentinel', function ($app) {
            $sentinel = new Sentinel(
                $app['sentinel.persistence'],
                $app['sentinel.users'],
                $app['sentinel.roles'],
                $app['sentinel.activations'],
                $app['events']
            );

            if (isset($app['sentinel.checkpoints'])) {
                foreach ($app['sentinel.checkpoints'] as $key => $checkpoint) {
                    $sentinel->addCheckpoint($key, $checkpoint);
                }
            }

            $sentinel->setActivationRepository($app['sentinel.activations']);
            $sentinel->setReminderRepository($app['sentinel.reminders']);

            $sentinel->setRequestCredentials(function () use ($app) {
                $request = $app['request'];

                $login = $request->getUser();
                $password = $request->getPassword();

                if ($login === null && $password === null) {
                    return;
                }

                return compact('login', 'password');
            });

            $sentinel->creatingBasicResponse(function () {
                $headers = ['WWW-Authenticate' => 'Basic'];

                return new Response('Invalid credentials.', 401, $headers);
            });

            return $sentinel;
        });

        $this->app->alias('sentinel', 'App\Sentinel');
    }
}
