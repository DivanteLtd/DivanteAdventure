<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;

class ChecklistDetailsMapper extends AbstractChecklistMapper
{
    private TaskDoneFilter $taskDoneFilter;

    public function __construct(TaskDoneFilter $taskDoneFilter)
    {
        $this->taskDoneFilter = $taskDoneFilter;
    }
    /**
     * @param Checklist $entity
     * @return array
     */
    public function mapEntity($entity): array
    {
        $data = $this->getBaseData($entity);
        $data['startedAt'] = $entity->getStartedAt()->getTimestamp();
        $data['tasks'] = [];
        /** @var ChecklistQuestion $question */
        foreach ($entity->getQuestions()->toArray() as $question) {
            $task = [
                'id' => $question->getId(),
                'namePl' => $question->getNamePl(),
                'nameEn' => $question->getNameEn(),
                'descriptionPl' => $question->getDescriptionPl(),
                'descriptionEn' => $question->getDescriptionEn(),
                'status' => $question->getCurrentStatus(),
                'possibleStatuses' => $question->getPossibleStatuses(),
            ];
            $responsible = $question->getResponsible();
            if (!is_null($responsible)) {
                $task['responsible'] = $this->getEmployeeData([$responsible]);
            }
            $checkDate = $question->getCheckedAt();
            if (!is_null($checkDate)) {
                $task['checkedAt'] = $checkDate->getTimestamp();
            }
            $data['tasks'][] = $task;
            $data['tasksFinishedCount'] = $entity->getQuestions()
                ->filter(\Closure::fromCallable($this->taskDoneFilter))
                ->count();
            $data['tasksAllCount'] = $entity->getQuestions()->count();
        }
        return $data;
    }

    public function __invoke(Checklist $checklist) : array
    {
        return $this->mapEntity($checklist);
    }
}
