<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\DeleteFAQQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQQuestion;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\EmployeeUserSuperAdmin;
use Tests\Utils\ObjectRepositoryMocker;

class DeleteFAQQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdPassedToFinder() : void
    {
        $message = new DeleteFAQQuestion(rand(0, 10000), rand(0, 10000));
        /** @var int|null $passedId */
        $passedId = null;
        $handler = $this->getHandler(
            function (int $id) use (&$passedId) {
                $passedId = $id;
                $question = new FAQQuestion();
                $question->setFAQCategory(new FAQCategory());
                return $question;
            },
    );
        $handler($message);
        $this->assertEquals($message->getId(), $passedId);
    }

    public function testIdNotFound() : void
    {
        $message = new DeleteFAQQuestion(rand(0, 10000), rand(0, 10000));
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
        $message = new DeleteFAQQuestion(rand(0, 10000), rand(0, 10000));
        $obj = new FAQQuestion();
        $obj->setFAQCategory(new FAQCategory());
        /** @var null|FAQQuestion $deleted */
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
        $this->assertInstanceOf(FAQQuestion::class, $deleted);
        $this->assertSame($obj, $deleted);
    }

    private function getHandler(callable $find, ?callable $remove = null) : DeleteFAQQuestionHandler
    {
        return new DeleteFAQQuestionHandler(
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
        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em->expects($this->any())->method('getRepository')->willReturnCallback(
            function (string $repoName) use ($repo, $employeeRepo) {
                if (strpos($repoName, 'Employee') !== false) {
                    return $employeeRepo;
                } elseif (strpos($repoName, 'FAQQuestion') !== false) {
                    return $repo;
                } else {
                    return null;
                }
            }
        );
        if (!is_null($removeCallback)) {
            $em->expects($this->any())->method('remove')->willReturnCallback($removeCallback);
        }
        return $em;
    }
}
