<?php
namespace Defnenders\Core;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

trait DatabaseAwareTrait
{
    protected $dbh;
    protected $query;

    public function getConnection()
    {
        return $this->dbh;
    }

    public function getQueryBuilder()
    {
        return $this->query;
    }

    public function setConnection(ExtendedPdo $dbh)
    {
        $this->dbh = $dbh;
    }

    public function setQueryBuilder(QueryFactory $query)
    {
        $this->query = $query;
    }
}
