<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;

interface ChecklistQuestionInterface
{
    public function getNamePl(): string;
    public function getNameEn(): string;
    public function getDescriptionPl(): string;
    public function getDescriptionEn(): string;
    /** @return array<int,array<string,string>> */
    public function getPossibleStatuses(): array;
    public function getResponsible(): ?Employee;
    public function getChecklist() : ChecklistInterface;
}
