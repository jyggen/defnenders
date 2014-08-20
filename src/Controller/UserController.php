<?php
namespace Defnenders\Controller;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Silex\Application;
use Silex\ControllerProviderInterface;

class UserController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/login', function () use ($app) {
            if ($app['session']->has('user')) {
                $app['session']->getFlashBag()->add('error', 'You are already logged in!');
                return $app->redirect($app->path('home'));
            }

            try {
                $session = (new FacebookRedirectLoginHelper($app->url('login')))->getSessionFromRedirect();
            } catch (Exception $e) {
                $app['session']->getFlashBag()->add('error', $e->getMessage());
                return $app->redirect($app->path('home'));
            }

            if ($session === null) {
                $app['session']->getFlashBag()->add('error', 'Unable to login. Please try again later!');
                return $app->redirect($app->path('home'));
            }

            try {
                $me = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
            } catch (Exception $e) {
                $app['session']->getFlashBag()->add('error', $e->getMessage());
                return $app->redirect($app->path('home'));
            }

            $app['session']->set('user', [
                'accessToken' => $session->getToken(),
                'id'          => $me->getId(),
                'name'        => $me->getName(),
            ]);

            return $app->redirect($app->path('home'));
        })->bind('login');

        $controllers->get('/logout', function () use ($app) {
            $app['session']->remove('user');
            return $app->redirect($app->path('home'));
        })->bind('logout');

        return $controllers;
    }
}
