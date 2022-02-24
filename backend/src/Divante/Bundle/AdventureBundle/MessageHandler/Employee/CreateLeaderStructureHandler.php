<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 27.03.19
 * Time: 10:39
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateLeaderStructure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateLeaderStructureHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param CreateLeaderStructure $message
     * @throws \Exception
     */
    public function __invoke(CreateLeaderStructure $message) : void
    {
        $employeeRepo = $this->em->getRepository(Employee::class);
        /** @var Employee $leader */
        $leader = $employeeRepo->find($message->getLeaderId());
        $this->em->getConnection()->beginTransaction();
        $employeeIds = $message->getLeaderStructure();
        foreach ($employeeIds as $employeeId) {
            /** @var Employee|null $employee */
            $employee = $this->em->getRepository(Employee::class)->find($employeeId);
            $currentLeaders = $employee->getLeaders();
            if (is_null($employee)) {
                throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $employeeId));
            }
            $flag = true;
            /** @var Employee|null $currentLeader */
            foreach ($currentLeaders as $currentLeader) {
                if ($currentLeader->getId() === $leader->getId()) {
                    $flag = false;
                }
            }
            if ($flag) {
                $employee->getLeaders()->add($leader);
                try {
                    $employee->setUpdatedAt();
                    $this->em->persist($employee);
                } catch (\Exception $exception) {
                    $this->em->getConnection()->rollBack();
                    throw new \Exception("Updating employee entry failed", 0, $exception);
                }
            }
        }
        $this->em->flush();
        $this->em->commit();
    }
}
