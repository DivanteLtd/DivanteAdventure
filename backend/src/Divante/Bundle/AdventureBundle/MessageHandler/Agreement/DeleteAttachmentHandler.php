<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\AgreementAttachment;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteAttachment;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteAttachmentHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteAttachment $message
     * @throws Exception
     */
    public function __invoke(DeleteAttachment $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        $entry = $em->getRepository(AgreementAttachment::class)->find($id);
        if (is_null($entry)) {
            throw new Exception("Occupancy entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
