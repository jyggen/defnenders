<?php
namespace Defnenders\Provider;

use Defnenders\Controller\WebController;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllerServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['controllers.web'] = $app->share(function () use ($app) {
            return new WebController($app['repository.armor_type'], $app['repository.class'], $app['repository.role'], $app['repository.token']);
        });
    }
}
