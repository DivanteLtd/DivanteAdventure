<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteChecklistTemplateQuestion
{
    use ObjectTrait;

    private int $templateId;
    private int $questionId;

    public function __construct(int $templateId, int $questionId)
    {
        $this->templateId = $templateId;
        $this->questionId = $questionId;
    }

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }
}
