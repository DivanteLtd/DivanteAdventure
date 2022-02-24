<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateFAQQuestion
{
    use ObjectTrait;
    private int $id;
    private int $employeeId;
    private int $categoryId;
    private string $questionPl;
    private string $questionEn;
    private string $answerPl;
    private string $answerEn;

    public function __construct(
        int $id,
        int $employeeId,
        int $categoryId,
        string $questionPl,
        string $questionEn,
        string $answerPl,
        string $answerEn
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->categoryId = $categoryId;
        $this->questionPl = $questionPl;
        $this->questionEn = $questionEn;
        $this->answerPl = $answerPl;
        $this->answerEn = $answerEn;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getQuestionPl(): string
    {
        return $this->questionPl;
    }

    public function getQuestionEn(): string
    {
        return $this->questionEn;
    }

    public function getAnswerPl(): string
    {
        return $this->answerPl;
    }

    public function getAnswerEn(): string
    {
        return $this->answerEn;
    }
}
