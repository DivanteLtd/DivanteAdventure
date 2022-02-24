<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Message\DeleteOccupancyEntry;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteOccupancyEntryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteOccupancyEntry $message
     * @throws Exception
     */
    public function __invoke(DeleteOccupancyEntry $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        $entry = $em->getRepository(EmployeeOccupancy::class)->find($id);
        if (is_null($entry)) {
            throw new Exception("Occupancy entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
