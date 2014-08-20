<?php
namespace Defnenders\Notifications;

use Silex\Application;
use Silex\ServiceProviderInterface;

class NotificationServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['notifications.controller'] = $app->share(function () use ($app) {
            return new NotificationController;
        });

        $app['notifications.me'] = function () use ($app) {
            return $app['notifications.repository']->getByUserId((int) $app['user_info']['id']);
        };

        $app['notifications.processor'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Notifications\NotificationProcessor', [$app['users.repository']]);
        });

        $app['notifications.repository'] = $app->share(function () use ($app) {
            return $app->resolve('Defnenders\Notifications\NotificationRepository');
        });
    }
}
