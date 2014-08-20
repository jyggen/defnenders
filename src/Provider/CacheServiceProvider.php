<?php
namespace Defnenders\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Stash\Driver\Redis;
use Stash\Pool;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {

    }

    public function register(Application $app)
    {
        $app['stash'] = $app->share(function () use ($app) {

            $driver = new Redis;

            $driver->setOptions([
                'servers' => [
                    [
                        'server' => $app['cache']['host'],
                        'port'   => $app['cache']['port'],
                        'ttl'    => 0
                    ],
                ],
            ]);

            $pool = new Pool($driver);

            $pool->setNamespace($app['cache']['namespace']);

            return $pool;
        });
    }
}
