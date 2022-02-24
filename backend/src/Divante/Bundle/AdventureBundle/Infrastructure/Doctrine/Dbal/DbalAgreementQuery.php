<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 12:22
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Query\Agreement\AgreementAttachmentView;
use Doctrine\DBAL\Connection;

class DbalAgreementQuery
{
    protected Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param int $id
     * @return AgreementAttachmentView[]
     */
    public function getAll(int $id): array
    {
        $agreements = $this->conn->fetchAll(
            'SELECT ag.id, ag.name_pl as agreementNamePl, ag.name_en as agreementNameEn, ag.descriptionPl,
                    ag.descriptionEn, ag.required, ag.priority, ag.type,
                    ag.contract_ids AS contracts,
                    (SELECT GROUP_CONCAT(a.id,\':\',a.name) 
                    FROM agreement_attachment aa LEFT JOIN attachment a on aa.attachment_id = a.id 
                    WHERE aa.agreement_id = ag.id) as files, 
                    (SELECT EXISTS(SELECT * from employee_agreement eg 
                    WHERE eg.employee_id = :id AND ag.id = eg.agreement_id))
                    as accepted FROM agreement ag
                    WHERE ag.type = '.Agreement::TYPE_GDPR .' OR ag.type = '.Agreement::TYPE_FIRE_SAFETY
                    .' OR ag.type = '.Agreement::TYPE_ISO,
            ['id' => $id]
        );
        return array_map(function ($pair) {
            $files = explode(',', $pair['files']);
            $attachmentsId = [];
            $name = [];
            $contracts = explode(',', $pair['contracts']);
            foreach ($files as $file) {
                $tmp = explode(':', $file);
                array_push($attachmentsId, $tmp[0]);
                array_push($name, $tmp[1]);
            }
            return new AgreementAttachmentView(
                $pair['id'],
                $pair['descriptionPl'],
                $pair['descriptionEn'],
                $pair['agreementNamePl'],
                $pair['agreementNameEn'],
                $pair['required'],
                $pair['priority'],
                $attachmentsId,
                $name,
                $contracts,
                $pair['accepted'],
                $pair['type'],
            );
        }, $agreements);
    }
}
