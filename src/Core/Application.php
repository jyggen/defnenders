<?php
namespace Defnenders\Core;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Notifications\NotificationServiceProvider;
use ReflectionClass;
use Silex\Application as SilexApplication;
use Silex\Application\UrlGeneratorTrait;

class Application extends SilexApplication
{
    use UrlGeneratorTrait;

    public function __construct()
    {
        parent::__construct();
        $this->register(new NotificationServiceProvider);
    }

    public function resolve($class, array $arguments = array())
    {
        $reflection = new ReflectionClass($class);
        $instance   = $reflection->newInstanceArgs($arguments);

        if ($instance instanceof CacheAwareInterface) {
            $instance->setCache($this['stash']);
        }

        if ($instance instanceof DatabaseAwareInterface) {
            $instance->setConnection($this['pdo']);
            $instance->setQueryBuilder($this['pdo.query']);
        }

        return $instance;
    }
}
