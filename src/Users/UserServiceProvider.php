<?php
namespace Defnenders\Users;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['users.repository'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Users\UserRepository');
        });
    }
}
