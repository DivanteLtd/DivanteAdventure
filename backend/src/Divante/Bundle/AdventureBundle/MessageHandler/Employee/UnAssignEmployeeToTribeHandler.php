<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 05.04.19
 * Time: 11:47
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\Tribe\TribeAssignmentChangeEvent;
use Divante\Bundle\AdventureBundle\Message\Employee\UnAssignEmployeeToTribe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UnAssignEmployeeToTribeHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UnAssignEmployeeToTribe $unAssignEmployeeToTribe
     * @throws \Exception
     */
    public function __invoke(UnAssignEmployeeToTribe $unAssignEmployeeToTribe) : void
    {
        $em = $this->em;
        $employee = $em->getRepository(Employee::class)->find($unAssignEmployeeToTribe->getUserId());
        $oldTribeId = null;
        if (!is_null($employee->getTribe()->getId())) {
            $oldTribeId = $employee->getTribe()->getId();
        }
        $employee->setTribe(null);
        try {
            $em->flush();
            $this->dispatcher->dispatch(new TribeAssignmentChangeEvent($employee->getId(), $oldTribeId, null));
        } catch (\Exception $exception) {
            throw new \Exception('Ups. We have a problem', 0, $exception);
        }
    }
}
