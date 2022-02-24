<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplateQuestion;
use Tests\FoundationTestCase;

class CreateChecklistTemplateQuestionTest extends FoundationTestCase
{
    public function testTemplateIdReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testNamePlReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($namePl, $message->getNamePl());
    }

    public function testNameEnReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($nameEn, $message->getNameEn());
    }

    public function testDescriptionPlReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($descPl, $message->getDescriptionPl());
    }

    public function testDescriptionEnReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($descEn, $message->getDescriptionEn());
    }

    public function testPossibleStatusesReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
            $namePl,
            $nameEn,
            $descPl,
            $descEn,
            $possibleStatuses,
            $responsibleId,
        );

        $this->assertEquals($possibleStatuses, $message->getPossibleStatuses());
    }

    public function testResponsibleIdValueReturnedCorrectly() : void
    {
        $templateId = rand(0, 10000);
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();
        $responsibleId = rand(0, 10000);

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
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
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $descPl = "RandomDescPl".rand(0, 10000);
        $descEn = "RandomDescEn".rand(0, 10000);
        $possibleStatuses = $this->generateStatuses();

        $message = new CreateChecklistTemplateQuestion(
            $templateId,
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
