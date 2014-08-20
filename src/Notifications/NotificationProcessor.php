<?php
namespace Defnenders\Notifications;

use Defnenders\Contract\CacheAwareInterface;
use Defnenders\Contract\DatabaseAwareInterface;
use Defnenders\Core\CacheAwareTrait;
use Defnenders\Core\DatabaseAwareTrait;

class NotificationProcessor implements CacheAwareInterface, DatabaseAwareInterface
{
    use CacheAwareTrait;
    use DatabaseAwareTrait;

    public function createForAllUsers($body)
    {

    }

    public function markAllAsReadForUserId($userId)
    {
        $delete = $this->query->newDelete();
        $query  = $delete->from('notifications')->where('member_id = :user_id')->bindValue('user_id', $userId);

        $this->dbh->perform($query->__toString(), $query->getBindValues());
        $this->cache->getItem('notifications/users/'.$userId)->clear();
    }

    public function markAsReadForUserIdById($userId, $notifcationId)
    {
        $delete = $this->query->newDelete();
        $query  = $delete->from('notifications')->where('member_id = :user_id')->bindValue('user_id', $userId)->where('id = :notify_id')->bindValue('notify_id', $notifcationId)->limit(1);

        $this->dbh->perform($query->__toString(), $query->getBindValues());
        $this->cache->getItem('notifications/users/'.$userId)->clear();
    }
}
