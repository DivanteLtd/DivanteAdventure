<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Message\Hardware\DeleteHardwareAgreementEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteHardwareAgreementEntryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteHardwareAgreementEntry $message
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public function __invoke(DeleteHardwareAgreementEntry $message) : void
    {
        $this->em->beginTransaction();
        $id = $message->getId();
        /** @var HardwareAgreement|null $entry */
        $entry = $this->em->getRepository(HardwareAgreement::class)->find($id);
        if (is_null($entry)) {
            throw new NotFoundHttpException("Hardware Agreement with id $id not found.");
        }
        $this->em->remove($entry);
        $this->em->flush();
        $this->em->commit();
    }
}
