<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\UpdateQuestionStatusHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\UpdateQuestionStatus;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class UpdateQuestionStatusHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testLookingForQuestion() : void
    {
        /** @var int|null $searchedId */
        $searchedId = null;
        $find = function (int $id) use (&$searchedId) : ?ChecklistQuestion {
            $searchedId = $id;
            return $this->generateQuestion();
        };
        $questionId = rand(0, 10000);

        $message = new UpdateQuestionStatus(rand(0, 10000), $questionId, 0, $this->generateEmployee());
        $this->getHandler($find)($message);

        $this->assertNotNull($searchedId);
        $this->assertEquals($searchedId, $questionId);
    }

    public function testExceptionOnQuestionNotFound() : void
    {
        /** @var int|null $searchedId */
        $searchedId = null;
        $find = fn() : ?ChecklistQuestion => null;
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), rand(0, 10000), $this->generateEmployee());

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*question.*not.*found.*/i');
        $this->getHandler($find)($message);
    }

    public function testChecklistUnitedEmployeeNotOwnerException() : void
    {
        $question = $this->generateQuestion();
        $question->getChecklist()->setType(Checklist::TYPE_UNITED);
        $question->getChecklist()->setOwners([]);
        $find = fn() : ?ChecklistQuestion => $question;
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), rand(0, 10000), $this->generateEmployee());

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessageMatches('/.*checklist.*united.*user.*not.*owner.*/i');
        $this->getHandler($find)($message);
    }

    public function testChecklistDistributedEmployeeNotResponsibleException() : void
    {
        $question = $this->generateQuestion();
        $question->setResponsible(null);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), rand(0, 10000), $this->generateEmployee());

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessageMatches('/.*checklist.*distributed.*user.*not.*responsible.*/i');
        $this->getHandler($find)($message);
    }

    public function testSelectedStatusNotAvailable() : void
    {
        $employee = $this->generateEmployee();
        $question = $this->generateQuestion();
        $question->setResponsible($employee);
        $question->setPossibleStatuses([[], [], []]);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $message = new UpdateQuestionStatus(
            rand(0, 10000),
            rand(0, 10000),
            count($question->getPossibleStatuses()) + 1,
            $employee
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*status.*not.*found.*/i');
        $this->getHandler($find)($message);
    }

    public function testStatusUpdated() : void
    {
        $employee = $this->generateEmployee();
        $question = $this->generateQuestion();
        $question->setResponsible($employee);
        $question->setPossibleStatuses([[], [], []]);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $newStatus = rand(0, count($question->getPossibleStatuses()) - 1);
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), $newStatus, $employee);

        $this->getHandler($find)($message);
        $this->assertEquals($question->getCurrentStatus(), $newStatus);
    }

    public function testUpdatedAtUpdated() : void
    {
        $employee = $this->generateEmployee();
        $question = $this->generateQuestion();
        $question->setResponsible($employee);
        $question->setPossibleStatuses([[], [], []]);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $newStatus = rand(0, count($question->getPossibleStatuses()) - 1);
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), $newStatus, $employee);

        $this->getHandler($find)($message);
        $this->assertEqualsWithDelta(new DateTime(), $question->getUpdatedAt(), 5);
    }

    public function testIfNotDoneCheckedAtIsNull() : void
    {
        $employee = $this->generateEmployee();
        $question = $this->generateQuestion();
        $question->setResponsible($employee);
        $question->setPossibleStatuses([[], [], []]);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $newStatus = rand(0, count($question->getPossibleStatuses()) - 1);
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), $newStatus, $employee);

        $this->getHandler($find)($message);
        $this->assertNull($question->getCheckedAt());
    }

    public function testIfDoneCheckedAtCorrectl() : void
    {
        $employee = $this->generateEmployee();
        $question = $this->generateQuestion();
        $question->setResponsible($employee);
        $question->setPossibleStatuses([['done' => true], [], []]);
        $question->getChecklist()->setType(Checklist::TYPE_DISTRIBUTED);
        $find = fn() : ?ChecklistQuestion => $question;
        $message = new UpdateQuestionStatus(rand(0, 10000), rand(0, 10000), 0, $employee);

        $this->getHandler($find)($message);
        $this->assertEqualsWithDelta(new DateTime(), $question->getCheckedAt(), 5);
    }

    private function generateQuestion() : ChecklistQuestion
    {
        $checklist = new Checklist();
        $checklist
            ->setType(rand(50, 500))
            ->setOwners([$this->generateEmployee()]);
        $question = new ChecklistQuestion();
        $question->setChecklist($checklist);
        $question->setResponsible(null);
        $question->setPossibleStatuses([[]]);
        return $question;
    }

    private function generateEmployee() : Employee
    {
        $employee = new Employee();
        $this->setId($employee, rand(0, 10000));
        return $employee;
    }

    private function getHandler(?callable $findQuestionCallback = null) : UpdateQuestionStatusHandler
    {
        $findQuestionCallback ??= fn() => $this->generateQuestion();
        $questionRepo = $this->mockObjectRepository($findQuestionCallback);

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($questionRepo);

        return new UpdateQuestionStatusHandler($em);
    }
}
