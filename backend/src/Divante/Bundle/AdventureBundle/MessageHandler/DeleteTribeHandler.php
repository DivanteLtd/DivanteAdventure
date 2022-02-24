<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Message\DeleteTribe;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteTribeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteTribe $message
     * @throws Exception
     */
    public function __invoke(DeleteTribe $message) : void
    {
        $em = $this->em;
        $em->getConnection()->beginTransaction();
        $id = $message->getEntryId();
        $tribe = $em->getRepository(Tribe::class)->find($id);
        if (is_null($tribe)) {
            throw new Exception("Tribe entry with id $id not found.");
        }
        $tribe->setVisibility(1);
        $em->persist($tribe);
        $em->flush();
        $em->commit();
    }
}
