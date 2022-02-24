<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Link;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeleteLink;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteLinkHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteLink $message
     * @throws Exception
     */
    public function __invoke(DeleteLink $message) : void
    {
        $id = $message->getId();
        $em = $this->em;
        $link = $em->getRepository(Link::class)->find($id);
        $em->beginTransaction();
        try {
            $em->remove($link);
            $em->flush();
            $em->commit();
        } catch (Exception $e) {
            $em->rollback();
            throw new Exception($e);
        }
    }
}
