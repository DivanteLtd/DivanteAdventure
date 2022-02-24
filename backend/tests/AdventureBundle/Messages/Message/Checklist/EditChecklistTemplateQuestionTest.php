<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplateQuestion;
use Tests\FoundationTestCase;

class EditChecklistTemplateQuestionTest extends FoundationTestCase
{
    public function testTemplateIdReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testQuestionIdReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($questionId, $message->getQuestionId());
    }

    public function testNamePlValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($namePl, $message->getNamePl());
    }

    public function testNamePlNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            null,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertNull($message->getNamePl());
    }

    public function testNameEnValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($nameEn, $message->getNameEn());
    }

    public function testNameEnNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            null,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertNull($message->getNameEn());
    }

    public function testDescriptionPlValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($descPl, $message->getDescriptionPl());
    }

    public function testDescriptionPlNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            null,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertNull($message->getDescriptionPl());
    }

    public function testDescriptionEnValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($descEn, $message->getDescriptionEn());
    }

    public function testDescriptionEnNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            null,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertNUll($message->getDescriptionEn());
    }

    public function testPossibleStatusesValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($possibleStatuses, $message->getPossibleStatuses());
    }

    public function testPossibleStatusesNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            null,
            $responsibleId,
        );

        $this->assertNull($message->getPossibleStatuses());
    }

    public function testResponsibleIdValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($responsibleId, $message->getResponsibleId());
    }

    public function testResponsibleIdNullReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();

        $message = new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            null,
        );

        $this->assertNull($message->getResponsibleId());
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
