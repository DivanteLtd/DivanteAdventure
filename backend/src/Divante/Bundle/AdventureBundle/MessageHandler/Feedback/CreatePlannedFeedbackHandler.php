<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\CreatePlannedFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreatePlannedFeedbackHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreatePlannedFeedback $message)
    {
        $employee = $this->getEmployee($message->getEmployeeId());
        $leader = $this->getEmployee($message->getLeaderId());
        $leaderId = $leader->getId();
        if (!$employee->isLeader($leaderId) && !$leader->getTribeResponsible()->contains($employee->getTribe())) {
            throw new AccessDeniedHttpException("You are not allowed to plan a feedback for this person");
        }

        $employeeFeedback = $this->em->getRepository(PlannedFeedback::class)
            ->findOneBy(['employee' => $employee]);
        if (!is_null($employeeFeedback)) {
            $this->em->remove($employeeFeedback);
            $this->em->flush();
        }

        $plannedFeedback = new PlannedFeedback();
        $plannedFeedback->setEmployee($employee)
            ->setLeader($leader)
            ->setDate(new \DateTime($message->getDate()))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($plannedFeedback);
        $this->em->flush();
    }

    private function getEmployee(int $id): Employee
    {
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($id);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with ID $id not found");
        }
        return $employee;
    }
}
