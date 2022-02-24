<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\CreateChecklistTemplateHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\FoundationTestCase;

class CreateChecklistTemplateHandlerTest extends FoundationTestCase
{
    public function testChecklistPersistCalled() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $persistedObject = null;

        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        $this->assertNotNull($persistedObject);
        $this->assertInstanceOf(ChecklistTemplate::class, $persistedObject);
    }

    public function testCorrectTypeSet() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var ChecklistTemplate $persistedObject */
        $this->assertEquals($message->getType(), $persistedObject->getType());
    }

    public function testCorrectNamePlSet() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var ChecklistTemplate $persistedObject */
        $this->assertEquals($message->getNamePl(), $persistedObject->getNamePl());
    }

    public function testCorrectNameEnSet() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var ChecklistTemplate $persistedObject */
        $this->assertEquals($message->getNameEn(), $persistedObject->getNameEn());
    }

    public function testCreatedAtSet() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $currentDate = new \DateTime();
        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var ChecklistTemplate $persistedObject */
        $this->assertEqualsWithDelta($currentDate, $persistedObject->getCreatedAt(), 5);
    }

    public function testUpdatedAtSet() : void
    {
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $currentDate = new \DateTime();
        $handler = new CreateChecklistTemplateHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var ChecklistTemplate $persistedObject */
        $this->assertEqualsWithDelta($currentDate, $persistedObject->getUpdatedAt(), 5);
    }

    private function generateMessage() : CreateChecklistTemplate
    {
        $type = rand(0, 10000);
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        return new CreateChecklistTemplate($type, $namePl, $nameEn);
    }
}
