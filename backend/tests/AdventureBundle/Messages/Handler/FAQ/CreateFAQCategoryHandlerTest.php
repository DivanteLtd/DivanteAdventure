<?php

namespace Tests\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\CreateFAQCategoryHandler;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQCategory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateFAQCategoryHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testNotFound() : void
    {
        $repo = $this->mockObjectRepository(fn() => null);
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist', 'getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches("/.*Employee.*not.*found.*/i");
        $handler($message);
    }

    public function testCreateFAQCategory() : void
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('find')->willReturn(new Employee());

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist', 'getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );
        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        $this->assertInstanceOf(FAQCategory::class, $persistedObject);
    }

    public function testCorrectNamePlSet() : void
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('find')->willReturn(new Employee());

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist', 'getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var FAQCategory $persistedObject */
        $this->assertEquals($message->getNamePl(), $persistedObject->getNamePl());
    }

    public function testCorrectNameEnSet() : void
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('find')->willReturn(new Employee());

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist', 'getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var FAQCategory $persistedObject */
        $this->assertEquals($message->getNameEn(), $persistedObject->getNameEn());
    }

    public function testCreatedAtSet() : void
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('find')->willReturn(new Employee());

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $currentDate = new \DateTime();
        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var FAQCategory $persistedObject */
        $this->assertEqualsWithDelta($currentDate, $persistedObject->getCreatedAt(), 5);
    }

    public function testUpdatedAtSet() : void
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('find')->willReturn(new Employee());

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['persist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $persistedObject = null;
        $em->expects($this->any())->method('persist')->willReturnCallback(
            function ($obj) use (&$persistedObject) {
                $persistedObject = $obj;
            }
        );

        $currentDate = new \DateTime();
        $handler = new CreateFAQCategoryHandler($em);
        $message = $this->generateMessage();
        $handler($message);
        /** @var CreateFAQCategory $persistedObject */
        $this->assertEqualsWithDelta($currentDate, $persistedObject->getUpdatedAt(), 5);
    }

    private function generateMessage() : CreateFAQCategory
    {
        $randomNamePl = 'RandomNamePl'.rand(0, 10000);
        $randomNameEn = 'RandomNameEn'.rand(0, 10000);
        $employees = [rand(0, 10000), rand(0, 10000)];
        return new CreateFAQCategory($employees, $randomNamePl, $randomNameEn);
    }
}
