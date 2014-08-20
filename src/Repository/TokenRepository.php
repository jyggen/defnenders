<?php
namespace Defnenders\Repository;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class TokenRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function all()
    {
        $item   = $this->cache->getItem('tokens');
        $tokens = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $select = $this->query->newSelect();
            $query  = $select->cols(['*'])->from('tokens')->orderBy(['name']);
            $tokens = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

            $item->set($tokens);
        }

        return $tokens;
    }
}
