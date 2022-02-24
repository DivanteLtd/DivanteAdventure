<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistTemplateMapper;
use Tests\FoundationTestCase;

class ChecklistTemplateMapperTest extends FoundationTestCase
{
    public function testMappedId() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($template->getId(), $result['id']);
    }

    public function testMappedNamePl() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('namePl', $result);
        $this->assertEquals($template->getNamePl(), $result['namePl']);
    }

    public function testMappedNameEn() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('nameEn', $result);
        $this->assertEquals($template->getNameEn(), $result['nameEn']);
    }

    public function testMappedType() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals($template->getType(), $result['type']);
    }

    public function testMappedCreatedAt() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertEquals($template->getUpdatedAt()->getTimestamp(), $result['createdAt']);
    }

    public function testMappedUpdatedAt() : void
    {
        $template = $this->getRandomTemplate();
        $result = $this->getMapper()->mapEntity($template);
        $this->assertArrayHasKey('updatedAt', $result);
        $this->assertEquals($template->getUpdatedAt()->getTimestamp(), $result['updatedAt']);
    }

    private function getMapper() : ChecklistTemplateMapper
    {
        return new ChecklistTemplateMapper();
    }

    private function getRandomTemplate() : ChecklistTemplate
    {
        $template = new ChecklistTemplate();
        $template->setNamePl("RandomNamePl".rand(0, 10000))
            ->setNameEn("RandomNameEn".rand(0, 10000))
            ->setType(rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->setId($template, rand(0, 10000));
        return $template;
    }
}
