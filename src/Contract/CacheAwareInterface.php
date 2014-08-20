<?php
namespace Defnenders\Contract;

use Stash\Pool;

interface CacheAwareInterface
{
    public function getCache();

    public function setCache(Pool $cache);
}
