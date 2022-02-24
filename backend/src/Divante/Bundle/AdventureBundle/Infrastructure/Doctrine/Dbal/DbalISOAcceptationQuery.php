<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\Agreement\ISOAcceptationQuery;
use Divante\Bundle\AdventureBundle\Query\Agreement\ISOAcceptationView;
use Doctrine\DBAL\Connection;

class DbalISOAcceptationQuery implements ISOAcceptationQuery
{
    protected Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /** @inheritdoc */
    public function get() :array
    {
        $employeeAgreements = $this->conn->fetchAll(
            'SELECT e.last_name, e.name, e.email, 
                    (SELECT GROUP_CONCAT(a.id SEPARATOR \';\')
                    FROM employee_agreement ea LEFT JOIN agreement a on ea.agreement_id = a.id 
                    WHERE ea.agreement_id = a.id AND ea.employee_id = e.id AND a.type = 3) as description,
                    (SELECT ea.updated_at FROM employee_agreement ea LEFT JOIN agreement a on ea.agreement_id = a.id 
                    WHERE ea.agreement_id = a.id AND ea.employee_id = e.id AND a.type = 3 LIMIT 1) as updated_at
                    FROM employee e WHERE e.hired_to is null OR e.hired_to > now() ORDER BY e.last_name ASC',
        );
        return array_map(function ($pair) {
            $descriptions = explode(';', $pair['description']);

            return new ISOAcceptationView(
                $pair['name'],
                $pair['last_name'],
                $pair['email'],
                $descriptions,
                $pair['updated_at'],
            );
        }, $employeeAgreements);
    }
}
