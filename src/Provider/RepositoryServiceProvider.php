<?php
namespace Defnenders\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['repository.armor_type'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Repository\ArmorTypeRepository');
        });

        $app['repository.class'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Repository\ClassRepository');
        });

        $app['repository.role'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Repository\RoleRepository');
        });

        $app['repository.token'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Repository\TokenRepository');
        });
    }
}
