<?php
namespace Defnenders\Provider;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\Profiler;
use Aura\SqlQuery\QueryFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PdoServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['pdo'] = $app->share(function () use ($app) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $app['database']['hostname'], $app['database']['database']);
            $pdo = new ExtendedPdo($dsn, $app['database']['username'], $app['database']['password'], [], [ExtendedPdo::ATTR_EMULATE_PREPARES => false]);

            $pdo->setProfiler(new Profiler);
            $pdo->getProfiler()->setActive(true);

            return $pdo;
        });

        $app['pdo.query'] = $app->share(function () {
            return new QueryFactory('mysql');
        });

        $app['stats'] = function () use ($app) {
            return round((microtime(true) - APP_START) * 1000).'ms / '.formatBytes(memory_get_peak_usage(true), 2).' / '.count($app['pdo']->getProfiler()->getProfiles()).' queries';
        };
    }
}
