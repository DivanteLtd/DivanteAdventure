<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\Account\AccountUnlockEvent;
use Divante\Bundle\AdventureBundle\Message\Employee\UnlockEmployee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UnlockEmployeeHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UnlockEmployee $message
     * @throws \Exception
     */
    public function __invoke(UnlockEmployee $message) : void
    {
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($message->getEmployeeId());
        if (is_null($employee)) {
            throw new \Exception("Employee with ID {${$message->getEmployeeId()}} has not been found");
        }
        $this->em->beginTransaction();
        try {
            $employee->resetLock();
            $this->em->flush();
            $this->dispatcher->dispatch(new AccountUnlockEvent($employee));
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            throw new \Exception('Ups. We have a problem', 0, $exception);
        }
    }
}
