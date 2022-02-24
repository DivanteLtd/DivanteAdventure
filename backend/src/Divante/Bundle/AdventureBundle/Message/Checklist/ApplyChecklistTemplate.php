<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class ApplyChecklistTemplate
{
    use ObjectTrait;

    private int $templateId;
    private array $ownerIds;
    private int $subjectId;
    private bool $hidden;
    private int $userEmployeeId;
    private string $dueDate;

    public function __construct(
        int $templateId,
        array $ownerIds,
        int $subjectId,
        bool $hidden,
        int $userEmployeeId,
        string $dueDate
    ) {
        $this->templateId = $templateId;
        $this->ownerIds = $ownerIds;
        $this->subjectId = $subjectId;
        $this->hidden = $hidden;
        $this->userEmployeeId = $userEmployeeId;
        $this->dueDate = $dueDate;
    }

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function getOwnerIds(): array
    {
        return $this->ownerIds;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getUserEmployeeId(): int
    {
        return $this->userEmployeeId;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }
}
