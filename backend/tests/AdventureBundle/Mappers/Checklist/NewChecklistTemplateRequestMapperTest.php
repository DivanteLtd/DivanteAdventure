<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistInterface;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\NewChecklistTemplateRequestMapper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\AdventureTestUtils;
use Tests\FoundationTestCase;

class NewChecklistTemplateRequestMapperTest extends FoundationTestCase
{
    public function testLackingType() : void
    {
        $data = [
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*type.*required.*/");
        $this->getMapper()->mapEntity($request);
    }

    public function testTypeIsNotNumber() : void
    {
        $data = [
            'type' => 'SomeString'.rand(0, 10000),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*type.*number.*/");
        $this->getMapper()->mapEntity($request);
    }

    public function testTypeIncorrectValue() : void
    {
        $data = [
            'type' => rand(1000, 10000),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*Correct.*type.*are.*/");
        $this->getMapper()->mapEntity($request);
    }

    public function testLackingNamePl() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*namePl.*required.*/");
        $this->getMapper()->mapEntity($request);
    }

    public function testLackingNameEn() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*nameEn.*required.*/");
        $this->getMapper()->mapEntity($request);
    }

    public function testMappedTypeCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);
        $this->assertEquals($data['type'], $result['type']);
    }

    public function testMappedNamePlCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);
        $this->assertEquals($data['namePl'], $result['namePl']);
    }

    public function testMappedNameEnCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);
        $this->assertEquals($data['nameEn'], $result['nameEn']);
    }

    public function testMappedToMessageTypeCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request);
        $this->assertEquals($data['type'], $result->getType());
    }

    public function testMappedToMessageNamePlCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request);
        $this->assertEquals($data['namePl'], $result->getNamePl());
    }

    public function testMappedToMessageNameEnCorrectly() : void
    {
        $data = [
            'type' => rand(ChecklistInterface::TYPE_UNITED, ChecklistInterface::TYPE_DISTRIBUTED),
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request);
        $this->assertEquals($data['nameEn'], $result->getNameEn());
    }

    private function getMapper() : NewChecklistTemplateRequestMapper
    {
        return new NewChecklistTemplateRequestMapper();
    }
}
