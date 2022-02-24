<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

abstract class AbstractAskedQuestionReaction
{
    use ObjectTrait;

    private int $questionId;
    private Employee $answerer;

    public function __construct(int $questionId, Employee $answerer)
    {
        $this->questionId = $questionId;
        $this->answerer = $answerer;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getAnswerer(): Employee
    {
        return $this->answerer;
    }
}
