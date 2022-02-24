<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\EditChecklistTemplateHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class EditChecklistTemplateHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    private const ORIGINAL_NAME_EN = 'OriginalNameEn';
    private const ORIGINAL_NAME_PL = 'OriginalNamePl';

    public function testIdPassedToFinder() : void
    {
        $message = new EditChecklistTemplate(rand(0, 10000), null, null);
        /** @var int|null $passedId */
        $passedId = null;
        $handler = $this->getHandler(
            function (int $id) use (&$passedId) {
                $passedId = $id;
                return new ChecklistTemplate();
            }
        );
        $handler($message);
        $this->assertEquals($message->getId(), $passedId);
    }

    public function testIdNotFound() : void
    {
        $message = new EditChecklistTemplate(rand(0, 10000), null, null);
        $handler = $this->getHandler(
            function () {
                return null;
            }
        );
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*ID.*not.*found.*/');
        $handler($message);
    }

    public function testNameNotUpdated() : void
    {
        $object = new ChecklistTemplate();
        $object
            ->setNameEn(self::ORIGINAL_NAME_EN)
            ->setNamePl(self::ORIGINAL_NAME_PL);
        $handler = $this->getHandler(
            function () use ($object) {
                return $object;
            }
        );
        $message = new EditChecklistTemplate(rand(0, 10000), null, null);
        $date = new \DateTime();
        $handler($message);
        $this->assertEquals(self::ORIGINAL_NAME_EN, $object->getNameEn());
        $this->assertEquals(self::ORIGINAL_NAME_PL, $object->getNamePl());
        $this->assertEqualsWithDelta($date, $object->getUpdatedAt(), 5);
    }

    public function testNamePlUpdated() : void
    {
        $namePl = "RandomNamePl".rand(0, 100000);
        $object = new ChecklistTemplate();
        $object
            ->setNameEn(self::ORIGINAL_NAME_EN)
            ->setNamePl(self::ORIGINAL_NAME_PL);
        $handler = $this->getHandler(
            function () use ($object) {
                return $object;
            }
        );
        $message = new EditChecklistTemplate(rand(0, 10000), $namePl, null);
        $date = new \DateTime();
        $handler($message);
        $this->assertEquals(self::ORIGINAL_NAME_EN, $object->getNameEn());
        $this->assertEquals($namePl, $object->getNamePl());
        $this->assertEqualsWithDelta($date, $object->getUpdatedAt(), 5);
    }

    public function testNameEnUpdated() : void
    {
        $nameEn = "RandomNameEn".rand(0, 100000);
        $object = new ChecklistTemplate();
        $object
            ->setNameEn(self::ORIGINAL_NAME_EN)
            ->setNamePl(self::ORIGINAL_NAME_PL);
        $handler = $this->getHandler(
            function () use ($object) {
                return $object;
            }
        );
        $message = new EditChecklistTemplate(rand(0, 10000), null, $nameEn);
        $date = new \DateTime();
        $handler($message);
        $this->assertEquals($nameEn, $object->getNameEn());
        $this->assertEquals(self::ORIGINAL_NAME_PL, $object->getNamePl());
        $this->assertEqualsWithDelta($date, $object->getUpdatedAt(), 5);
    }

    public function testBothNamesUpdated() : void
    {
        $namePl = "RandomNamePl".rand(0, 100000);
        $nameEn = "RandomNameEn".rand(0, 100000);
        $object = new ChecklistTemplate();
        $object
            ->setNameEn(self::ORIGINAL_NAME_EN)
            ->setNamePl(self::ORIGINAL_NAME_PL);
        $handler = $this->getHandler(
            function () use ($object) {
                return $object;
            }
        );
        $message = new EditChecklistTemplate(rand(0, 10000), $namePl, $nameEn);
        $date = new \DateTime();
        $handler($message);
        $this->assertEquals($nameEn, $object->getNameEn());
        $this->assertEquals($namePl, $object->getNamePl());
        $this->assertEqualsWithDelta($date, $object->getUpdatedAt(), 5);
    }

    private function getHandler(callable $find) : EditChecklistTemplateHandler
    {
        return new EditChecklistTemplateHandler(
            $this->buildEntityManager($find),
        );
    }

    private function buildEntityManager(callable $findCallback) : EntityManagerInterface
    {
        $repo = $this->mockObjectRepository($findCallback);
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);
        return $em;
    }
}
