<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Doctrine\Common\Collections\Collection;

interface ChecklistInterface
{
    /** In united checklists all tasks have to be done by one person. */
    public const TYPE_UNITED = 1;
    /** In distributed checklist every task can have its own responsible person. */
    public const TYPE_DISTRIBUTED = 2;

    public function getType(): int;
    public function getNamePl(): string;
    public function getNameEn(): string;
    /** @return Collection<int,ChecklistQuestionInterface> */
    public function getQuestions() : Collection;
}
