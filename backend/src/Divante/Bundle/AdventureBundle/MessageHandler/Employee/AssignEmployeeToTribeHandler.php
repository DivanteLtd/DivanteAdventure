<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 05.04.19
 * Time: 11:47
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Events\Tribe\TribeAssignmentChangeEvent;
use Divante\Bundle\AdventureBundle\Message\Employee\AssignEmployeeToTribe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AssignEmployeeToTribeHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param AssignEmployeeToTribe $assignEmployeeToTribe
     * @throws \Exception
     */
    public function __invoke(AssignEmployeeToTribe $assignEmployeeToTribe) : void
    {
        $em = $this->em;
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($assignEmployeeToTribe->getUserId());
        /** @var Tribe|null $tribe */
        $tribe = $em->getRepository(Tribe::class)->find($assignEmployeeToTribe->getTribeId());
        if (is_null($employee)) {
            throw new \Exception("Employee with ID ".$assignEmployeeToTribe->getUserId()." has not been found");
        }
        if (is_null($tribe)) {
            throw new \Exception("Tribe with ID ".$assignEmployeeToTribe->getTribeId()." has not been found");
        }
        $oldTribeId = null;
        if (!is_null($employee->getTribe())) {
            $oldTribeId = $employee->getTribe()->getId();
        }
        $employee
            ->setTribe($tribe)
            ->setTechTribeLeader(false)
            ->setPosition(null)
            ->setLevel(null);
        try {
            $em->flush();
            $this->dispatcher->dispatch(
                new TribeAssignmentChangeEvent($employee->getId(), $oldTribeId, $tribe->getId())
            );
        } catch (\Exception $exception) {
            throw new \Exception('Assigning employee to tribe failed', 0, $exception);
        }
    }
}
