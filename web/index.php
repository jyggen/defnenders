<?php
define('APP_START', microtime(true));

require_once __DIR__.'/../vendor/autoload.php';

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSession;

use Defnenders\Core\Application;
use Defnenders\Provider\CacheServiceProvider;
use Defnenders\Provider\ControllerServiceProvider;
use Defnenders\Provider\PdoServiceProvider;
use Defnenders\Provider\RepositoryServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

use Symfony\Component\HttpFoundation\Request;

ErrorHandler::register(null, false);
ExceptionHandler::register(false);

$app = new Application;

$app->register(new ConfigServiceProvider(__DIR__.'/../settings.toml', ['base_dir' => realpath(__DIR__.'/..')]));

$app->register(new MonologServiceProvider, [
    'monolog.logfile' => $app['log']['file'],
    'monolog.level'   => $app['log']['level'],
]);

$app->register(new TwigServiceProvider, [
    'twig.path'    => $app['template']['path'],
    'twig.options' => [
        'debug' => $app['debug'],
        'cache' => $app['cache']['path'],
    ]
]);

$app->register(new UrlGeneratorServiceProvider);
$app->register(new CacheServiceProvider);
$app->register(new ControllerServiceProvider);
$app->register(new PdoServiceProvider);
$app->register(new RepositoryServiceProvider);
$app->register(new ServiceControllerServiceProvider);
$app->register(new SessionServiceProvider);

$app['user_info'] = function () use ($app) {
    return $app['session']->get('user');
};
$app['is_logged_in'] = function () use ($app) {
    return $app['session']->has('user');
};
$app['login_url'] = function () use ($app) {
    return (new FacebookRedirectLoginHelper($app->url('login')))->getLoginUrl(['public_profile']);
};

FacebookSession::setDefaultApplication($app['facebook']['id'], $app['facebook']['secret']);

$app->get('/', 'controllers.web:indexAction')->bind('home');
$app->get('/notifications', 'notifications.controller:markAllAsReadAction')->bind('markAllNotificationsAsRead');
$app->get('/notifications/{notificationId}', 'notifications.controller:markAsReadAction')->bind('markNotificationAsRead');
$app->mount('/user', new Defnenders\Controller\UserController);

$app->post('/character', function (Request $request) use ($app) {
    if ($app['is_logged_in'] === false) {
        $app['session']->getFlashBag()->add('error', 'You\'re not a logged in!');
        return $app->redirect($app->path('home'));
    }

    if ($request->request->has('spec_id') === false or $request->request->has('nickname') === false) {
        $app['session']->getFlashBag()->add('error', 'You really thought that would work?');
        return $app->redirect($app->path('home'));
    }

    $select = $app['pdo.query']->newSelect();
    $query  = $select->cols(['*'])->from('specializations')->where('id = :spec_id')->limit(1)->bindValue('spec_id', $request->request->get('spec_id'));
    $spec   = $app['pdo']->fetchOne($query->__toString(), $query->getBindValues());

    if ($spec === false) {
        $app['session']->getFlashBag()->add('error', 'You really thought that would work?');
        return $app->redirect($app->path('home'));
    }

    $select = $app['pdo.query']->newSelect();
    $query  = $select->cols(['*'])->from('members')->where('id = :member_id')->limit(1)->bindValue('member_id', $app['user_info']['id']);
    $me     = $app['pdo']->fetchOne($query->__toString(), $query->getBindValues());

    if ($me === false) {
        $insert = $app['pdo.query']->newInsert();
        $query  = $insert->into('members')->cols([
            'id'                => $app['user_info']['id'],
            'specialization_id' => $request->request->get('spec_id'),
            'name'              => $app['user_info']['name'],
            'nickname'          => $request->request->get('nickname'),
        ]);
        $app['pdo']->perform($query->__toString(), $query->getBindValues());
        return $app->redirect($app->path('home'));
    }

    if ($me['specialization_id'] !== (int) $request->request->get('spec_id') or $me['nickname'] !== $request->request->get('nickname')) {
        $update = $app['pdo.query']->newUpdate();
        $query  = $update->table('members')->cols([
            'specialization_id' => $request->request->get('spec_id'),
            'name'              => $app['user_info']['name'],
            'nickname'          => $request->request->get('nickname'),
        ])->where('id = :user_id')->bindValue('user_id', $app['user_info']['id']);
        $app['pdo']->perform($query->__toString(), $query->getBindValues());
    }

    return $app->redirect($app->path('home'));
})->bind('character');

$app->run();
