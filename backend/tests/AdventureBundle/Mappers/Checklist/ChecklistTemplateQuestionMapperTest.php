<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistTemplateQuestionMapper;
use Tests\FoundationTestCase;

class ChecklistTemplateQuestionMapperTest extends FoundationTestCase
{
    public function testSameResultsFromMapAndInvoke() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $resultA = $mapper->mapEntity($question);
        $resultB = $mapper($question);
        $this->assertEquals($resultA, $resultB);
    }

    public function testIdPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getId(), $result['id']);
    }

    public function testNamePlPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getNamePl(), $result['namePl']);
    }

    public function testNameEnPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getNameEn(), $result['nameEn']);
    }

    public function testDescriptionPlPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getDescriptionPl(), $result['descriptionPl']);
    }

    public function testDescriptionEnPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getDescriptionEn(), $result['descriptionEn']);
    }

    public function testPossibleStatusesPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($question->getPossibleStatuses(), $result['possibleStatuses']);
    }

    public function testResponsibleIdPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $responsible = $question->getResponsible();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($responsible->getId(), $result['responsible']['id']);
    }

    public function testResponsibleNamePassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $responsible = $question->getResponsible();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($responsible->getName(), $result['responsible']['name']);
    }

    public function testResponsibleLastNamePassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $responsible = $question->getResponsible();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($responsible->getLastName(), $result['responsible']['lastName']);
    }

    public function testResponsiblePhotoPassed() : void
    {
        $mapper = $this->getMapper();
        $question = $this->generateQuestion();
        $responsible = $question->getResponsible();
        $result = $mapper->mapEntity($question);
        $this->assertEquals($responsible->getPhoto(), $result['responsible']['photo']);
    }

    private function getMapper() : ChecklistTemplateQuestionMapper
    {
        return new ChecklistTemplateQuestionMapper();
    }

    private function generateQuestion() : ChecklistTemplateQuestion
    {
        $template = new ChecklistTemplateQuestion();
        $this->setId($template, rand(1, 10000));
        $template->setNamePl("RandomNamePl".rand(0, 10000));
        $template->setNameEn("RandomNameEn".rand(0, 10000));
        $template->setDescriptionPl("RandomDescriptionPl".rand(0, 10000));
        $template->setDescriptionEn("RandomDescriptionEn".rand(0, 10000));
        $template->setPossibleStatuses($this->generateStatuses());
        $template->setResponsible($this->generateEmployee());
        return $template;
    }

    private function generateEmployee() : Employee
    {
        $employee = new Employee();
        $this->setId($employee, rand(1, 10000));
        $employee
            ->setName("RandomName".rand(0, 10000))
            ->setLastName("RandomLastName".rand(0, 10000))
            ->setPhoto("RandomPhoto".rand(0, 10000));
        return $employee;
    }

    private function generateStatuses() : array
    {
        $count = rand(1, 10000);
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $status = $this->generateStatus();
            if ($i === 0) {
                $status['default'] = true;
            }
            $result[] = $status;
        }
        return $result;
    }

    private function generateStatus() : array
    {
        return [
            'label_pl' => "RandomLabelPl".rand(0, 10000),
            'label_en' => "RandomLabelEn".rand(0, 10000),
            'icon' => "RandomIcon".rand(0, 10000),
            'color' => "RandomColor".rand(0, 10000),
            'done' => rand(0, 10) < 5,
        ];
    }
}
