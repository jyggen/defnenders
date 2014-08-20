<?php
namespace Defnenders\Contract;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

interface DatabaseAwareInterface
{
    public function getConnection();

    public function getQueryBuilder();

    public function setConnection(ExtendedPdo $dbh);

    public function setQueryBuilder(QueryFactory $query);
}
