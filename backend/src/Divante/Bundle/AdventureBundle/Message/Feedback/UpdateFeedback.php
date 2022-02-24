<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class UpdateFeedback extends AbstractFeedbackMessage
{
    private int $id;
    private Employee $leader;
    private int $dateCreated;

    public function __construct(
        int $id,
        Employee $leader,
        ?string $feedback,
        ?string $progressFeedback,
        ?string $technicalFeedback,
        int $dateCreated
    ) {
        parent::__construct($feedback, $progressFeedback, $technicalFeedback);
        $this->id = $id;
        $this->leader = $leader;
        $this->dateCreated = $dateCreated;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getLeader() : Employee
    {
        return $this->leader;
    }

    public function getDateCreated() : int
    {
        return $this->dateCreated;
    }
}
