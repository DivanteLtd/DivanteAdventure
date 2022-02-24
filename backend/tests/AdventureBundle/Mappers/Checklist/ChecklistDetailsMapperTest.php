<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistDetailsMapper;
use Tests\FoundationTestCase;

class ChecklistDetailsMapperTest extends FoundationTestCase
{
    public function testSameResult() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $resultA = $mapper->mapEntity($checklist);
        $resultB = $mapper($checklist);
        $this->assertEquals($resultA, $resultB);
    }

    public function testIdPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $this->assertEquals($checklist->getId(), $result['id']);
    }

    public function testTypePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $this->assertEquals($checklist->getType(), $result['type']);
    }

    public function testNamePlPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $this->assertEquals($checklist->getNamePl(), $result['namePl']);
    }

    public function testNameEnPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $this->assertEquals($checklist->getNameEn(), $result['nameEn']);
    }

    public function testFinishedAtPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        /** @var \DateTime $finishedAt */
        $finishedAt = $checklist->getFinishedAt();
        $this->assertEquals($finishedAt->getTimestamp(), $result['finishedAt']);
    }

    public function testStartedAtPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        /** @var \DateTime $startedAt */
        $startedAt = $checklist->getStartedAt();
        $this->assertEquals($startedAt->getTimestamp(), $result['startedAt']);
    }

    public function testSubjectIdPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employee = $result['subject'];
        $this->assertEquals($checklist->getSubject()->getId(), $employee[0]['id']);
    }

    public function testSubjectNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employee = $result['subject'];
        $this->assertEquals($checklist->getSubject()->getName(), $employee[0]['name']);
    }

    public function testSubjectLastNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employee = $result['subject'];
        $this->assertEquals($checklist->getSubject()->getLastName(), $employee[0]['lastName']);
    }

    public function testSubjectPhotoPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employee = $result['subject'];
        $this->assertEquals($checklist->getSubject()->getPhoto(), $employee[0]['photo']);
    }

    public function testOwnerIdPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employees = $result['owners'];
        /** @var Employee $owner */
        $owners = $checklist->getOwners()->toArray();
        $this->assertEquals($owners[0]->getId(), $employees[0]['id']);
    }

    public function testOwnerNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employees = $result['owners'];
        /** @var Employee $owner */
        $owners = $checklist->getOwners()->toArray();
        $this->assertEquals($owners[0]->getName(), $employees[0]['name']);
    }

    public function testOwnerLastNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employees = $result['owners'];
        /** @var Employee $owner */
        $owners = $checklist->getOwners()->toArray();
        $this->assertEquals($owners[0]->getLastName(), $employees[0]['lastName']);
    }

    public function testOwnerPhotoPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $employees = $result['owners'];
        /** @var Employee $owner */
        $owners = $checklist->getOwners()->toArray();
        $this->assertEquals($owners[0]->getPhoto(), $employees[0]['photo']);
    }

    public function testAllTasksPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        $questions = $checklist->getQuestions();
        $this->assertEquals($questions->count(), count($tasks));
    }

    public function testTasksNamePlPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            $this->assertEquals($questions[$i]->getNamePl(), $tasks[$i]['namePl']);
        }
    }

    public function testTasksNameEnPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            $this->assertEquals($questions[$i]->getNameEn(), $tasks[$i]['nameEn']);
        }
    }

    public function testTasksStatusPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            $this->assertEquals($questions[$i]->getCurrentStatus(), $tasks[$i]['status']);
        }
    }

    public function testTasksPossibleStatusesPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            $this->assertEquals($questions[$i]->getPossibleStatuses(), $tasks[$i]['possibleStatuses']);
        }
    }

    public function testTasksCheckedAtPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            /** @var \DateTime $checkedAt */
            $checkedAt = $questions[$i]->getCheckedAt();
            $this->assertEquals($checkedAt->getTimestamp(), $tasks[$i]['checkedAt']);
        }
    }

    public function testResponsibleIdPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            /** @var Employee $responsible */
            $responsible = $questions[$i]->getResponsible();
            $this->assertEquals($responsible->getId(), $tasks[$i]['responsible'][0]['id']);
        }
    }

    public function testResponsibleNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            /** @var Employee $responsible */
            $responsible = $questions[$i]->getResponsible();
            $this->assertEquals($responsible->getName(), $tasks[$i]['responsible'][0]['name']);
        }
    }

    public function testResponsibleLastNamePassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            /** @var Employee $responsible */
            $responsible = $questions[$i]->getResponsible();
            $this->assertEquals($responsible->getLastName(), $tasks[$i]['responsible'][0]['lastName']);
        }
    }

    public function testResponsiblePhotoPassed() : void
    {
        $checklist = $this->prepareRandomChecklist();
        $mapper = $this->getMapper();
        $result = $mapper->mapEntity($checklist);
        $tasks = $result['tasks'];
        /** @var ChecklistQuestion[] $questions */
        $questions = $checklist->getQuestions()->toArray();
        for ($i = 0; $i < count($tasks); $i++) {
            /** @var Employee $responsible */
            $responsible = $questions[$i]->getResponsible();
            $this->assertEquals($responsible->getPhoto(), $tasks[$i]['responsible'][0]['photo']);
        }
    }

    private function prepareRandomChecklist() : Checklist
    {
        $checklist = new Checklist();
        $this->setId($checklist, rand(1, 10000));
        $checklist->setType(rand(0, 10000))
            ->setNamePl("RandomNamePl".rand(0, 10000))
            ->setNameEn("RandomNameEn".rand(0, 10000))
            ->setStartedAt(new \DateTime())
            ->setFinishedAt(new \DateTime())
            ->setDueDate(new \DateTime())
            ->setOwners([$this->prepareRandomEmployee()])
            ->setSubject($this->prepareRandomEmployee());
        $tasksReady = rand(5, 100);
        $tasksToDo = $tasksReady + rand(5, 100);
        for ($i = 0; $i < $tasksToDo; $i++) {
            $question = new ChecklistQuestion();
            $this->setId($question, rand(1, 10000));
            $question->setNameEn("RandomNameEn".rand(0, 10000));
            $question->setNamePl("RandomNamePl".rand(0, 10000));
            $question->setDescriptionPl("RandomDescriptionPl".rand(0, 10000));
            $question->setDescriptionEn("RandomDescriptionEn".rand(0, 10000));
            $question->setPossibleStatuses([
                [
                    'done' => true,
                ],[
                    'done' => false,
                ]
            ]);
            $question->setResponsible($this->prepareRandomEmployee());
            $question->setCheckedAt(new \DateTime());
            if ($i < $tasksReady) {
                $question->setCurrentStatus(0);
            } else {
                $question->setCurrentStatus(1);
            }
            $checklist->getQuestions()->add($question);
        }
        return $checklist;
    }

    private function prepareRandomEmployee() : Employee
    {
        $employee = new Employee();
        $this->setId($employee, rand(1, 10000));
        $employee->setName("RandomName".rand(0, 10000))
            ->setLastName("RandomLastName".rand(0, 10000))
            ->setPhoto("RandomPhoto".rand(0, 10000));
        return $employee;
    }

    private function getMapper() : ChecklistDetailsMapper
    {
        $filter = new TaskDoneFilter();
        return new ChecklistDetailsMapper($filter);
    }
}
