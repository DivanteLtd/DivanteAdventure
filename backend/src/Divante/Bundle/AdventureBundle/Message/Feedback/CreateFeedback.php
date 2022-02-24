<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class CreateFeedback extends AbstractFeedbackMessage
{
    private int $employeeId;
    private Employee $leader;
    private int $type;
    private ?string $dateCreated;

    public function __construct(
        int $employeeId,
        Employee $leader,
        ?string $feedback,
        ?string $progressFeedback,
        ?string $technicalFeedback,
        int $type,
        ?string $dateCreated
    ) {
        parent::__construct($feedback, $progressFeedback, $technicalFeedback);
        $this->employeeId = $employeeId;
        $this->leader = $leader;
        $this->type = $type;
        $this->dateCreated = $dateCreated;
    }

    public function getEmployeeId() : int
    {
        return $this->employeeId;
    }

    public function getLeader() : Employee
    {
        return $this->leader;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function getDateCreated() : ?string
    {
        return $this->dateCreated;
    }
}
