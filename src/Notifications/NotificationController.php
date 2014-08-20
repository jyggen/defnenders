<?php
namespace Defnenders\Notifications;

use Defnenders\Core\Application;

class NotificationController
{
    public function markAllAsReadAction(Application $app)
    {
        $app['notifications.processor']->markAllAsReadForUserId((int) $app['user_info']['id']);
        return $app->redirect($app->path('home'));
    }

    public function markAsReadAction(Application $app, $notificationId)
    {
        $app['notifications.processor']->markAsReadForUserIdById((int) $app['user_info']['id'], $notificationId);
        return $app->redirect($app->path('home'));
    }
}
