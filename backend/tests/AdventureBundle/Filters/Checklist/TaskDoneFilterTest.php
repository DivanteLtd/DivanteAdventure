<?php

namespace Tests\AdventureBundle\Filters\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;
use Tests\FoundationTestCase;

class TaskDoneFilterTest extends FoundationTestCase
{
    public function testNotDone() : void
    {
        $question = new ChecklistQuestion();
        $question->setPossibleStatuses([
            [
                'done' => true,
            ],
            [
                'done' => false,
            ],
        ]);
        $question->setCurrentStatus(1);
        $filter = new TaskDoneFilter();
        $this->assertFalse($filter($question));
    }

    public function testDone() : void
    {
        $question = new ChecklistQuestion();
        $question->setPossibleStatuses([
            [
                'done' => true,
            ],
            [
                'done' => false,
            ],
        ]);
        $question->setCurrentStatus(0);
        $filter = new TaskDoneFilter();
        $this->assertTrue($filter($question));
    }
}
