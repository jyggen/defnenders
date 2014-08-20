<?php
namespace Defnenders\Core;

use Stash\Pool;

trait CacheAwareTrait
{
    protected $cache;

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache(Pool $cache)
    {
        $this->cache = $cache;
    }
}
