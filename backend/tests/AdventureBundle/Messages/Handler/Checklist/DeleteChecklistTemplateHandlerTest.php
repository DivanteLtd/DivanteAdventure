<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\DeleteChecklistTemplateHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class DeleteChecklistTemplateHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdPassedToFinder() : void
    {
        $message = new DeleteChecklistTemplate(rand(0, 10000));
        /** @var int|null $passedId */
        $passedId = null;
        $handler = $this->getHandler(
            function (int $id) use (&$passedId) {
                $passedId = $id;
                return new ChecklistTemplate();
            }
        );
        $handler($message);
        $this->assertEquals($message->getChecklistId(), $passedId);
    }

    public function testIdNotFound() : void
    {
        $message = new DeleteChecklistTemplate(rand(0, 10000));
        $handler = $this->getHandler(
            function () {
                return null;
            }
        );
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*ID.*not.*found.*/');
        $handler($message);
    }

    public function testDeleted() : void
    {
        $message = new DeleteChecklistTemplate(rand(0, 10000));
        $obj = new ChecklistTemplate();
        /** @var null|ChecklistTemplate $deleted */
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
        $this->assertInstanceOf(ChecklistTemplate::class, $deleted);
        $this->assertSame($obj, $deleted);
    }

    private function getHandler(callable $find, ?callable $remove = null) : DeleteChecklistTemplateHandler
    {
        return new DeleteChecklistTemplateHandler(
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
