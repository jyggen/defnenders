<?php
namespace Defnenders\Repository;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class ClassRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function all()
    {
        $item    = $this->cache->getItem('classes');
        $classes = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $select  = $this->query->newSelect();
            $query   = $select->cols(['*'])->from('classes')->orderBy(['name']);
            $classes = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

            $item->set($classes);
        }

        return $classes;
    }
}
