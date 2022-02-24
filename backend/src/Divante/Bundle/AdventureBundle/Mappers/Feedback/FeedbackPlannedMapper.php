<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Mappers\AbstractDoctrineAwareMapper;

class FeedbackPlannedMapper extends AbstractDoctrineAwareMapper
{
    /**
     * @param PlannedFeedback $plannedFeedback
     * @return array<string,mixed>
     */
    public function __invoke(PlannedFeedback $plannedFeedback): array
    {
        $em = $this->getObjectManager();
        $employee = $plannedFeedback->getEmployee();
        /** @var Feedback[] $employeeFeedbacks */
        $employeeFeedbacks = $em->getRepository(Feedback::class)
            ->findBy(['employee' => $employee], ['createdAt' => 'DESC']);
        if (!empty($employeeFeedbacks)) {
            /** @var Feedback $lastFeedbackDate */
            $lastFeedbackDate = end($employeeFeedbacks)->getDateCreated()->format('Y-m-d');
        }
        $employee = [
            'id' => $employee->getId(),
            'name' => $plannedFeedback->getEmployee()->getName(),
            'lastName' => $plannedFeedback->getEmployee()->getLastName(),
            'email' => $plannedFeedback->getEmployee()->getEmail(),
            'photo' => $plannedFeedback->getEmployee()->getPhoto(),
            'lastFeedbackDate' => $lastFeedbackDate ?? null
        ];
        $leader = [
            'id' => $plannedFeedback->getLeader()->getId(),
            'name' => $plannedFeedback->getLeader()->getName(),
            'lastName' => $plannedFeedback->getLeader()->getLastName(),
            'photo' => $plannedFeedback->getLeader()->getPhoto(),
        ];
        return [
            'id' => $plannedFeedback->getId(),
            'employee' => $employee,
            'leader' => $leader,
            'date' => $plannedFeedback->getDate()->format('Y-m-d'),
            'updatedAt' => $plannedFeedback->getUpdatedAt()->getTimestamp()
        ];
    }
}
