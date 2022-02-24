<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Employee\DeletePadawan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeletePadawanHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param DeletePadawan $message
     * @throws \Exception
     */
    public function __invoke(DeletePadawan $message) : void
    {
        $this->em->getConnection()->beginTransaction();
        $employeeRepo = $this->em->getRepository(Employee::class);
        /** @var Employee $padawan */
        $padawan = $employeeRepo->find($message->getPadawanId());
        $padawanLeadersCollection = $padawan->getLeaders();
        $padawanLeaders = $padawanLeadersCollection->getValues();
        /** @var Employee $padawanLeader */
        foreach ($padawanLeaders as $key => $padawanLeader) {
            if ($padawanLeader->getId() === $message->getLeaderId()) {
                $padawanLeadersCollection->remove($key);
            }
        }
        $this->em->flush();
        $this->em->commit();
    }
}
