<?php
namespace Defnenders\Notifications;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class NotificationRepository implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function getByUserId($userId)
    {
        $item = $this->cache->getItem('notifications/users/'.$userId);
        $data = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $select = $this->query->newSelect();
            $query  = $select->cols(['*'])->from('notifications')->where('member_id = :user_id')->bindValue('user_id', $userId);
            $data   = $this->dbh->fetchAssoc($query->__toString(), $query->getBindValues());

            $item->set($data);
        }

        return $data;
    }
}
