<?php

namespace Tests\Entrypoints\Api\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractFeedbackTest extends AbstractEntrypointTest
{
    public function generateFeedback(Employee $leader, Employee $padawan) : Feedback
    {
        $feedback = new Feedback();
        $feedback->setEmployee($padawan)
            ->setLeader($leader)
            ->setType(Feedback::TECH_FEEDBACK)
            ->setFeedback("RandomText".rand(0, 10000))
            ->setUpdatedAt()
            ->setCreatedAt();
        $this->em->persist($feedback);
        return $feedback;
    }
}
