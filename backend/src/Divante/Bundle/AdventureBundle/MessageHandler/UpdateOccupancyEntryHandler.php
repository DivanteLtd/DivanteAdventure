<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 10:04
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Message\UpdateOccupancyEntry;
use Doctrine\ORM\EntityManagerInterface;

class UpdateOccupancyEntryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateOccupancyEntry $message
     * @throws \Exception
     */
    public function __invoke(UpdateOccupancyEntry $message) : void
    {
        $em = $this->em;

        $entry = $message->getEntry();
        $employee = $message->getEmployee();
        $project = $message->getProject();
        $timestamp = $message->getTimestamp();
        $occupancy = $message->getOccupancy();

        if (!is_null($employee)) {
            $entry->setEmployee($employee);
        }

        if (!is_null($project)) {
            $entry->setProject($project);
        }

        if (!is_null($timestamp)) {
            $entry->setDate($timestamp);
        }

        if (!is_null($occupancy)) {
            $entry->setOccupancy($occupancy);
        }

        $em->getConnection()->beginTransaction();
        try {
            $em->persist($entry);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw new \Exception("Updating occupancy entry failed", 0, $e);
        }
    }
}
