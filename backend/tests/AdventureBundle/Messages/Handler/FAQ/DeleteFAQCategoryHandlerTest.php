<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\DeleteFAQCategoryHandler;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQCategory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class DeleteFAQCategoryHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdPassedToFinder() : void
    {
        $message = new DeleteFAQCategory(rand(0, 10000));
        /** @var int|null $passedId */
        $passedId = null;
        $handler = $this->getHandler(
            function (int $id) use (&$passedId) {
                $passedId = $id;
                return new FAQCategory();
            }
        );
        $handler($message);
        $this->assertEquals($message->getId(), $passedId);
    }

    public function testIdNotFound() : void
    {
        $message = new DeleteFAQCategory(rand(0, 10000));
        $handler = $this->getHandler(
            function () {
                return null;
            }
        );
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*id.*not.*found.*/');
        $handler($message);
    }

    public function testDeleted() : void
    {
        $message = new DeleteFAQCategory(rand(0, 10000));
        $obj = new FAQCategory();
        /** @var null|FAQCategory $deleted */
        $deleted = null;
        $handler = $this->getHandler(
            function () use ($obj) {
                return $obj;
            },
            function ($toDelete) use (&$deleted) {
                $deleted = $toDelete;
            }
        );
        $handler($message);
        $this->assertNotNull($deleted);
        $this->assertInstanceOf(FAQCategory::class, $deleted);
        $this->assertSame($obj, $deleted);
    }

    private function getHandler(callable $find, ?callable $remove = null) : DeleteFAQCategoryHandler
    {
        return new DeleteFAQCategoryHandler(
            $this->buildEntityManager($find, $remove),
        );
    }

    private function buildEntityManager(callable $findCallback, ?callable $removeCallback) : EntityManagerInterface
    {
        $repo = $this->mockObjectRepository($findCallback);
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'remove'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);
        if (!is_null($removeCallback)) {
            $em->expects($this->any())->method('remove')->willReturnCallback($removeCallback);
        }
        return $em;
    }
}
