<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Mappers\Checklist\EditChecklistTemplateQuestionRequestMapper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\AdventureTestUtils;
use Tests\FoundationTestCase;

class EditChecklistTemplateQuestionRequestMapperTest extends FoundationTestCase
{
    public function testTemplateIdPassed() : void
    {
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, $id, rand(0, 10000));
        $this->assertEquals($result->getTemplateId(), $id);
    }

    public function testQuestionIdPassed() : void
    {
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), $id);
        $this->assertEquals($result->getQuestionId(), $id);
    }

    public function testResponsibleIdPassed() : void
    {
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'responsibleId' => $id,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getResponsibleId(), $id);
    }

    public function testResponsibleIdNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getResponsibleId());
    }

    public function testResponsibleIdWrongType() : void
    {
        $request = AdventureTestUtils::createRequest([
            'responsibleId' => "StringIsNotInt",
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*type.*integer.*/");
        $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
    }

    public function testNamePlPassed() : void
    {
        $val = "NamePl".rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'namePl' => $val,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getNamePl(), $val);
    }

    public function testNamePlNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getNamePl());
    }

    public function testNameEnPassed() : void
    {
        $val = "NameEn".rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'nameEn' => $val,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getNameEn(), $val);
    }

    public function testNameEnNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getNameEn());
    }

    public function testDescriptionPlPassed() : void
    {
        $val = "DescriptionPl".rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'descriptionPl' => $val,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getDescriptionPl(), $val);
    }

    public function testDescriptionPlNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getDescriptionPl());
    }

    public function testDescriptionEnPassed() : void
    {
        $val = "DescriptionEn".rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'descriptionEn' => $val,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getDescriptionEn(), $val);
    }

    public function testDescriptionEnNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getDescriptionEn());
    }

    public function testPossibleStatusesPassed() : void
    {
        $val = [ 'foo' => 'bar'.rand(0, 10000) ];
        $request = AdventureTestUtils::createRequest([
            'possibleStatuses' => $val,
        ]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertEquals($result->getPossibleStatuses(), $val);
    }

    public function testPossibleStatusesNull() : void
    {
        $request = AdventureTestUtils::createRequest([]);
        $result = $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
        $this->assertNull($result->getPossibleStatuses());
    }

    public function testPossibleStatusesWrongType() : void
    {
        $request = AdventureTestUtils::createRequest([
            'possibleStatuses' => "StringIsNotArray",
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*type.*array.*/");
        $this->getMapper()->mapToMessage($request, rand(0, 10000), rand(0, 10000));
    }

    private function getMapper() : EditChecklistTemplateQuestionRequestMapper
    {
        return new EditChecklistTemplateQuestionRequestMapper();
    }
}
