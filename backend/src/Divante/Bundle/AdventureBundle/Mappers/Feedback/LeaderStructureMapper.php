<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Mappers\AbstractDoctrineAwareMapper;

class LeaderStructureMapper extends AbstractDoctrineAwareMapper
{
    /**
     * @param array $structure
     * @return array<string|int, mixed>
     */
    public function __invoke(array $structure): array
    {
        $em = $this->getObjectManager();
        $result = [];
        /** @var Employee $employee */
        foreach ($structure as $employee) {
            $plannedFeedback = $em->getRepository(PlannedFeedback::class)
                ->findOneBy(['employee' => $employee]);
            $plannedFeedbackDate = null;
            if (!empty($plannedFeedback)) {
                /** @var PlannedFeedback $plannedFeedbackDate */
                $plannedFeedbackDate = $plannedFeedback->getDate()->format('Y-m-d');
            }
            /** @var Feedback[] $employeeFeedbacks */
            $employeeFeedbacks = $em->getRepository(Feedback::class)
                ->findBy(['employee' => $employee], ['dateCreated' => 'ASC']);
            $lastFeedbackDate = null;
            if (!empty($employeeFeedbacks)) {
                /** @var Feedback $lastFeedbackDate */
                $lastFeedbackDate = end($employeeFeedbacks)->getDateCreated()->format('Y-m-d');
            }
            $today = (new \DateTime())->format('Y-m-d');
            if ($employee->getHiredTo() === null || $employee->getHiredTo()->format('Y-m-d') >= $today) {
                $result[] = [
                    'id' => $employee->getId(),
                    'name' => $employee->getName(),
                    'lastName' => $employee->getLastName(),
                    'email' => $employee->getPhoto(),
                    'lastFeedbackDate' => $lastFeedbackDate,
                    'plannedFeedbackDate' => $plannedFeedbackDate
                ];
            }
        }
        return $result;
    }
}
