<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Mappers\Checklist\NewChecklistTemplateQuestionRequestMapper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\AdventureTestUtils;
use Tests\FoundationTestCase;

class NewChecklistTemplateQuestionRequestMapperTest extends FoundationTestCase
{
    public function testPassedTemplateId() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $templateId = rand(1, 10000);
        $message = $this->getMapper()->mapToMessage($request, $templateId);
        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testLackingResponsibleId() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['responsibleId']);
        $request = AdventureTestUtils::createRequest($data);

        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertNull($message->getResponsibleId());
    }

    public function testWrongTypeResponsibleId() : void
    {
        $data = $this->prepareCorrectData();
        $data['responsibleId'] = "StringIsNotInt";
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*responsibleId.*int.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedResponsibleId() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['responsibleId'], $message->getResponsibleId());
    }

    public function testLackingNamePl() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['namePl']);
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*namePl.*required.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedNamePl() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['namePl'], $message->getNamePl());
    }

    public function testLackingNameEn() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['nameEn']);
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*nameEn.*required.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedNameEn() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['nameEn'], $message->getnameEn());
    }

    public function testLackingDescriptionPl() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['descriptionPl']);
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*descriptionPl.*required.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedDescriptionPl() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['descriptionPl'], $message->getdescriptionPl());
    }

    public function testLackingDescriptionEn() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['descriptionEn']);
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*descriptionEn.*required.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedDescriptionEn() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['descriptionEn'], $message->getdescriptionEn());
    }

    public function testLackingPossibleStatuses() : void
    {
        $data = $this->prepareCorrectData();
        unset($data['possibleStatuses']);
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*possibleStatuses.*required.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testWrongTypePossibleStatuses() : void
    {
        $data = $this->prepareCorrectData();
        $data['possibleStatuses'] = "StringIsNotArray";
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*possibleStatuses.*array.*/");
        $this->getMapper()->mapToMessage($request, rand(1, 10000));
    }

    public function testPassedPossibleStatuses() : void
    {
        $data = $this->prepareCorrectData();
        $request = AdventureTestUtils::createRequest($data);
        $message = $this->getMapper()->mapToMessage($request, rand(1, 10000));
        $this->assertEquals($data['possibleStatuses'], $message->getpossibleStatuses());
    }

    private function getMapper() : NewChecklistTemplateQuestionRequestMapper
    {
        return new NewChecklistTemplateQuestionRequestMapper();
    }

    private function prepareCorrectData() : array
    {
        return [
            'namePl' => "NamePl".rand(0, 10000),
            'nameEn' => "NameEn".rand(0, 10000),
            'descriptionPl' => "DescriptionPl".rand(0, 10000),
            'descriptionEn' => "DescriptionEn".rand(0, 10000),
            'possibleStatuses' => [
                [
                    'name_pl' => "RandomStatusName".rand(0, 10000),
                ]
            ],
            'responsibleId' => rand(1, 10000),
        ];
    }
}
