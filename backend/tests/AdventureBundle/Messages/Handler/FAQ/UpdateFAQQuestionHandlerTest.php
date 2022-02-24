<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\UpdateFAQQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQQuestion;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class UpdateFAQQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdNotFound() : void
    {
        $message = new UpdateFAQQuestion(
            rand(0, 10000),
            rand(0, 10000),
            rand(0, 10000),
            'RandomQuestionPl'.rand(0, 10000),
            'RandomQuestionEn'.rand(0, 10000),
            'RandomAnswerPl'.rand(0, 10000),
            'RandomAnswerEn'.rand(0, 10000)
        );
        $handler = $this->getHandler(
            function () {
                return null;
            }
        );
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*id.*not.*found.*/');
        $handler($message);
    }


    private function getHandler(callable $find) : UpdateFAQQuestionHandler
    {
        return new UpdateFAQQuestionHandler(
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
