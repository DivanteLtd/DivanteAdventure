<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class RejectQuestion extends AbstractAskedQuestionReaction
{
    private string $rejectMessage;

    public function __construct(int $questionId, Employee $answerer, string $rejectMessage)
    {
        parent::__construct($questionId, $answerer);
        $this->rejectMessage = $rejectMessage;
    }

    public function getRejectMessage(): string
    {
        return $this->rejectMessage;
    }
}
