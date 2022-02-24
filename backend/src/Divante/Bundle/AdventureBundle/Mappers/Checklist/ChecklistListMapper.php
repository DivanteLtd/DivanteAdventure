<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;

class ChecklistListMapper extends AbstractChecklistMapper
{
    private TaskDoneFilter $taskDoneFilter;

    public function __construct(TaskDoneFilter $taskDoneFilter)
    {
        $this->taskDoneFilter = $taskDoneFilter;
    }

    /**
     * @param Checklist $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $data = $this->getBaseData($entity);
        $data['tasksFinishedCount'] = $entity->getQuestions()
            ->filter(\Closure::fromCallable($this->taskDoneFilter))
            ->count();
        $data['tasksAllCount'] = $entity->getQuestions()->count();
        return $data;
    }
}
