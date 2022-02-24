<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\DeleteChecklistTemplateQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplateQuestion;;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class DeleteChecklistTemplateQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testSearchingForQuestion() : void
    {
        /** @var int|null $searchedId */
        $searchedId = null;
        $findCallback = function (int $id) use (&$searchedId) {
            $searchedId = $id;
            return new ChecklistTemplateQuestion();
        };

        $id = rand(0, 10000);
        $handler = $this->getHandler($findCallback);
        $message = new DeleteChecklistTemplateQuestion(rand(0, 10000), $id);

        $handler($message);
        $this->assertNotNull($searchedId);
        $this->assertEquals($id, $searchedId);
    }

    public function testExceptionOnNotFound() : void
    {
        $findCallback = function () {
            return null;
        };

        $handler = $this->getHandler($findCallback);
        $message = new DeleteChecklistTemplateQuestion(rand(0, 10000), rand(0, 10000));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches("/.*question.*not.*found.*/i");
        $handler($message);
    }

    public function testRemovalCalled() : void
    {
        /** @var ChecklistTemplateQuestion|null $removedEntity */
        $removedEntity = null;
        $deleteCallback = function (ChecklistTemplateQuestion $entity) use (&$removedEntity) {
            $removedEntity = $entity;
        };

        $question = new ChecklistTemplateQuestion();
        $handler = $this->getHandler($question, $deleteCallback);
        $message = new DeleteChecklistTemplateQuestion(rand(0, 10000), rand(0, 10000));

        $handler($message);
        $this->assertNotNull($removedEntity);
        $this->assertSame($question, $removedEntity);
    }

    private function getHandler(
        $findReturnOrCallback = null,
        ?callable $deleteCallback = null
    ) : DeleteChecklistTemplateQuestionHandler {
        $findCallback = fn() => null;
        if (is_callable($findReturnOrCallback)) {
            $findCallback = $findReturnOrCallback;
        } elseif ($findReturnOrCallback instanceof ChecklistTemplateQuestion) {
            $findCallback = fn() => $findReturnOrCallback;
        }
        $repo = $this->mockObjectRepository($findCallback);
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'remove'])
            ->getMockForAbstractClass();
        $em->expects($this->any())
            ->method('getRepository')->willReturn($repo);
        $em->expects($this->any())
            ->method('remove')->willReturnCallback($deleteCallback ?? fn() => null);

        return new DeleteChecklistTemplateQuestionHandler($em);
    }
}
