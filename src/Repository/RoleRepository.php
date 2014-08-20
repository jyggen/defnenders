<?php
namespace Defnenders\Repository;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class RoleRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function all()
    {
        $item  = $this->cache->getItem('roles');
        $roles = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $select = $this->query->newSelect();
            $query  = $select->cols(['*'])->from('roles')->orderBy(['name']);
            $roles  = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

            $item->set($roles);
        }

        return $roles;
    }
}
