<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.03.19
 * Time: 14:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\EmployeeAgreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteEmployeeAgreement;
use Doctrine\ORM\EntityManagerInterface;

class DeleteEmployeeAgreementHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteEmployeeAgreement $message
     * @throws \Exception
     */
    public function __invoke(DeleteEmployeeAgreement $message) : void
    {
        $em = $this->em;
        $employeeId = $message->getEmployeeId();
        $marketingAgreements = $em->getRepository(Agreement::class)->findBy(['type' => 1]);
        foreach ($marketingAgreements as $agreement) {
            $agreementId = $agreement->getId();
            $entry = $em->getRepository(EmployeeAgreement::class)->findBy(
                [
                    'employee' => $employeeId,
                    'agreement' => $agreementId
                ]
            );
            if (count($entry) > 0) {
                $em->remove($entry[0]);
                $em->flush();
            }
        }
    }
}
