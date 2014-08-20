<?php
namespace Defnenders\Users;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class UserRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function all()
    {
        // @todo: cache this baby!
        $select = $this->query->newSelect();
        $query  = $select->cols(['*'])->from('members');
        $data   = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

        return $data;
    }
}
