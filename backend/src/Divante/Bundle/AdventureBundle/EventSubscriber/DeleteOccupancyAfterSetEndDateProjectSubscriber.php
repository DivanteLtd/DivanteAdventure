<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Events\ProjectOccupancyEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeleteOccupancyAfterSetEndDateProjectSubscriber
 *
 * @package Divante\Bundle\AdventureBundle\EventSubscriber
 * @author PK <pk@divante.com>
 */
class DeleteOccupancyAfterSetEndDateProjectSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() :array
    {
        return [
          ProjectOccupancyEvent::class => [
                ['deleteOccupancy', 0]
          ]
        ];
    }

    public function deleteOccupancy(ProjectOccupancyEvent $event)
    {
        $timestamp = $event->getProject()->getEndedAtTimestamp();
        if ($timestamp !== -1) {
            $qb = $this->em->createQueryBuilder();
            $qb->delete('AdventureBundle:EmployeeOccupancy', 'eo')
                ->andWhere('eo.project = :project')
                ->andWhere('eo.date >= :date')
                ->setParameters(['project' => $event->getProject(), 'date' => $timestamp])
                ->getQuery()
                ->execute();
        }
    }
}
