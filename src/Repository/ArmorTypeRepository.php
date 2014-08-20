<?php
namespace Defnenders\Repository;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class ArmorTypeRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function all()
    {
        $item   = $this->cache->getItem('armor_types');
        $armors = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $select = $this->query->newSelect();
            $query  = $select->cols(['*'])->from('armor_types')->orderBy(['name']);
            $armors = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

            $item->set($armors);
        }

        return $armors;
    }
}
