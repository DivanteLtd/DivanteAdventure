<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Mappers\Checklist\EditChecklistTemplateRequestMapper;
use Tests\AdventureTestUtils;
use Tests\FoundationTestCase;

class EditChecklistTemplateRequestMapperTest extends FoundationTestCase
{
    public function testEmptyRequest() : void
    {
        $data = [];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);

        $this->assertArrayHasKey('namePl', $result);
        $this->assertArrayHasKey('nameEn', $result);
        $this->assertNull($result['namePl']);
        $this->assertNull($result['nameEn']);
    }

    public function testRequestWithNameEn() : void
    {
        $data = [
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);

        $this->assertArrayHasKey('namePl', $result);
        $this->assertArrayHasKey('nameEn', $result);
        $this->assertNull($result['namePl']);
        $this->assertEquals($data['nameEn'], $result['nameEn']);
    }

    public function testRequestWithNamePl() : void
    {
        $data = [
            'namePl' => 'RandomNamePl'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);

        $this->assertArrayHasKey('namePl', $result);
        $this->assertArrayHasKey('nameEn', $result);
        $this->assertEquals($data['namePl'], $result['namePl']);
        $this->assertNull($result['nameEn']);
    }

    public function testRequestWithBothNames() : void
    {
        $data = [
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapEntity($request);

        $this->assertArrayHasKey('namePl', $result);
        $this->assertArrayHasKey('nameEn', $result);
        $this->assertEquals($data['namePl'], $result['namePl']);
        $this->assertEquals($data['nameEn'], $result['nameEn']);
    }

    public function testEmptyRequestToMessage() : void
    {
        $data = [];
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request, $id);

        $this->assertNull($result->getNamePl());
        $this->assertNull($result->getNameEn());
        $this->assertEquals($id, $result->getId());
    }

    public function testRequestWithNameEnToMessage() : void
    {
        $data = [
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request, $id);

        $this->assertNull($result->getNamePl());
        $this->assertEquals($data['nameEn'], $result->getNameEn());
        $this->assertEquals($id, $result->getId());
    }

    public function testRequestWithNamePlToMessage() : void
    {
        $data = [
            'namePl' => 'RandomNamePl'.rand(0, 10000),
        ];
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request, $id);

        $this->assertEquals($data['namePl'], $result->getNamePl());
        $this->assertNull($result->getNameEn());
        $this->assertEquals($id, $result->getId());
    }

    public function testRequestWithBothNamesToMessage() : void
    {
        $data = [
            'namePl' => 'RandomNamePl'.rand(0, 10000),
            'nameEn' => 'RandomNameEn'.rand(0, 10000),
        ];
        $id = rand(0, 10000);
        $request = AdventureTestUtils::createRequest($data);
        $result = $this->getMapper()->mapToMessage($request, $id);

        $this->assertEquals($data['namePl'], $result->getNamePl());
        $this->assertEquals($data['nameEn'], $result->getNameEn());
        $this->assertEquals($id, $result->getId());
    }

    private function getMapper() : EditChecklistTemplateRequestMapper
    {
        return new EditChecklistTemplateRequestMapper();
    }
}
