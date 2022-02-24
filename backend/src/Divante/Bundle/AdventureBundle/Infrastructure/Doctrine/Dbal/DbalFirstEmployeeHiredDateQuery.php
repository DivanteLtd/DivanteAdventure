<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\Employee\FirstEmployeeHiredDateQuery;
use Doctrine\DBAL\Connection;

class DbalFirstEmployeeHiredDateQuery implements FirstEmployeeHiredDateQuery
{
    private Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /** @inheritDoc */
    public function getFirstHiredDate(): array
    {
        return $this->conn->fetchAll(
            'SELECT hired_at AS hiredAt FROM employee WHERE hired_at IS NOT NULL ORDER BY hired_at ASC LIMIT 1'
        );
    }
}
