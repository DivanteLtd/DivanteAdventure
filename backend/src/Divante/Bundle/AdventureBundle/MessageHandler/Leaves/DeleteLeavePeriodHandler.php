<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Message\Leaves\DeleteLeavePeriod;
use Doctrine\ORM\EntityManagerInterface;

class DeleteLeavePeriodHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteLeavePeriod $message
     * @throws \Exception
     */
    public function __invoke(DeleteLeavePeriod $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        $entry = $em->getRepository(LeavePeriod::class)->find($id);
        if (is_null($entry)) {
            throw new \Exception("Leave period entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
