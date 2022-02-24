<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class AskQuestion
{
    use ObjectTrait;

    private Employee $questioner;
    private string $question;
    private int $categoryId;

    public function __construct(Employee $questioner, string $question, int $categoryId)
    {
        $this->questioner = $questioner;
        $this->question = $question;
        $this->categoryId = $categoryId;
    }

    public function getQuestioner(): Employee
    {
        return $this->questioner;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
