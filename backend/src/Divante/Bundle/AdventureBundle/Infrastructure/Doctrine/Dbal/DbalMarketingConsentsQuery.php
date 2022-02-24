<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 5.06.2019
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Doctrine\DBAL\Connection;

class DbalMarketingConsentsQuery
{
    protected Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param int $id
     * @return array<int,array<string,mixed>>
     */
    public function getAll(int $id): array
    {
        return $this->conn->fetchAll(
            'SELECT ag.id, ag.name_pl as agreementNamePl, ag.name_en as agreementNameEn, ag.descriptionPl, 
                ag.descriptionEn, ag.required, ag.priority, ag.type,
                (SELECT EXISTS(SELECT * from employee_agreement eg 
                WHERE eg.employee_id = :id AND ag.id = eg.agreement_id))
                as accepted FROM agreement ag WHERE ag.type = '.Agreement::TYPE_MARKETING,
            ['id' => $id]
        );
    }
}
