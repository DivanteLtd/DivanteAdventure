<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Mappers\Employee\MinimalisticEmployeeMapper;

class FeedbackMapper
{
    private MinimalisticEmployeeMapper $employeeMapper;

    public function __construct(MinimalisticEmployeeMapper $employeeMapper)
    {
        $this->employeeMapper = $employeeMapper;
    }

    /**
     * @param Feedback $feedback
     * @return array<string,mixed>
     */
    public function __invoke(Feedback $feedback): array
    {
        return [
            'id' => $feedback->getId(),
            'employee' => $this->employeeMapper->mapEmployeeToJson($feedback->getEmployee()),
            'leader' => $this->employeeMapper->mapEmployeeToJson($feedback->getLeader()),
            'feedback' => $feedback->getFeedback() ?? '',
            'progressFeedback' => $feedback->getProgressFeedback() ?? '',
            'technicalFeedback' => $feedback->getTechnicalFeedback() ?? '',
            'type' => $feedback->getType(),
            'updatedAt' => $feedback->getUpdatedAt()->getTimestamp(),
            'dateCreated' => $feedback->getDateCreated() ? $feedback->getDateCreated()->getTimestamp() : ''
        ];
    }
}
