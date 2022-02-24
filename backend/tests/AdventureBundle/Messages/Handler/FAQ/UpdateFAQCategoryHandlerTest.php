<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\UpdateFAQCategoryHandler;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQCategory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class UpdateFAQCategoryHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdNotFound() : void
    {
        $message = new UpdateFAQCategory(
            rand(0, 10000),
            [rand(0, 10000), rand(0, 10000)],
            'RandomNamePl'.rand(0, 10000),
            'RandomNameEn'.rand(0, 10000)
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


    private function getHandler(callable $find) : UpdateFAQCategoryHandler
    {
        return new UpdateFAQCategoryHandler(
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
