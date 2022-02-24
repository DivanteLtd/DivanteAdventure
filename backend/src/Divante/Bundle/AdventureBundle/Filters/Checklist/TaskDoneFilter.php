<?php

namespace Divante\Bundle\AdventureBundle\Filters\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestionInterface;

class TaskDoneFilter
{
    public function __invoke(ChecklistQuestionInterface $question) : bool
    {
        if (!$question instanceof ChecklistQuestion) {
            return false;
        }
        /** @var ChecklistQuestion $question */
        $possibleStatuses = $question->getPossibleStatuses();
        $currentStatus = $question->getCurrentStatus();
        $statusDescription = $possibleStatuses[$currentStatus] ?? [];
        return $statusDescription['done'] ?? false;
    }
}
