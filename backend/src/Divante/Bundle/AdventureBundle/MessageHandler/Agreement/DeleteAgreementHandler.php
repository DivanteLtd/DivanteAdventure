<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteAgreement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteAgreementHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteAgreement $message
     * @throws Exception
     */
    public function __invoke(DeleteAgreement $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        $entry = $em->getRepository(Agreement::class)->find($id);
        if (is_null($entry)) {
            throw new Exception("Agreement entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
