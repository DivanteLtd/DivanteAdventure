<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\PingChecklistQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\PingChecklistQuestion;
use Divante\Bundle\AdventureBundle\Services\ChecklistQuestionPinger;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class PingChecklistQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testSearchingForQuestion() : void
    {
        $searchedValue = null;
        $questionRepo = $this->mockObjectRepository(function ($val) use (&$searchedValue) : ChecklistQuestion {
            $searchedValue = $val;
            return $this->question();
        });
        $message = $this->message();
        $this->handler($questionRepo)($message);
        $this->assertIsInt($searchedValue);
    }

    public function testCorrectIdPassed() : void
    {
        /** @var int|null $searchedValue */
        $searchedValue = null;
        $questionRepo = $this->mockObjectRepository(function (int $val) use (&$searchedValue) : ChecklistQuestion {
            $searchedValue = $val;
            return $this->question();
        });
        $message = $this->message();
        $this->handler($questionRepo)($message);
        $this->assertEquals($message->getQuestionId(), $searchedValue);
    }

    public function testExceptionOnQuestionNotFound() : void
    {
        $questionRepo = $this->mockObjectRepository(fn() => null);
        $message = $this->message();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*question.*not.*found.*/i');
        $this->handler($questionRepo)($message);
    }

    public function testExceptionIfTaskIsDone() : void
    {
        $questionRepo = $this->mockObjectRepository(fn() => $this->question(true));
        $message = $this->message();
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*question.*done.*/i');
        $this->handler($questionRepo)($message);
    }

    public function testExceptionIfQuestionWasAlreadyPingedToday() : void
    {
        $questionRepo = $this->mockObjectRepository(fn() => $this->question()->setLastPingDate(new \DateTime()));
        $message = $this->message();
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*question.*pinged.*/i');
        $this->handler($questionRepo)($message);
    }

    public function testPingCalled() : void
    {
        $called = false;
        $pingFunc = function () use (&$called) : void {
            $called = true;
        };
        $message = $this->message();
        $this->handler(null, $pingFunc)($message);
        $this->assertTrue($called);
    }

    public function testCorrectEmployeePinged() : void
    {
        /** @var Employee|null $tested */
        $tested = null;
        $pingFunc = function (Employee $val) use (&$tested) : void {
            $tested = $val;
        };
        $question = $this->question();
        $questionRepo = $this->mockObjectRepository(fn() => $question);
        $message = $this->message();
        $this->handler($questionRepo, $pingFunc)($message);
        $this->assertNotNull($tested);
        $this->assertSame($question->getResponsible(), $tested);
    }

    public function testCorrectEmployeePinging() : void
    {
        /** @var Employee|null $tested */
        $tested = null;
        $pingFunc = function ($pinged, Employee $val) use (&$tested) : void {
            $tested = $val;
        };
        $question = $this->question();
        $questionRepo = $this->mockObjectRepository(fn() => $question);
        $message = $this->message();
        $this->handler($questionRepo, $pingFunc)($message);
        $this->assertNotNull($tested);
        $this->assertSame($message->getUser(), $tested);
    }

    public function testCorrectQuestionPinged() : void
    {
        /** @var ChecklistQuestion|null $tested */
        $tested = null;
        $pingFunc = function ($pinged, $pinging, ChecklistQuestion $val) use (&$tested) : void {
            $tested = $val;
        };
        $question = $this->question();
        $questionRepo = $this->mockObjectRepository(fn() => $question);
        $message = $this->message();
        $this->handler($questionRepo, $pingFunc)($message);
        $this->assertNotNull($tested);
        $this->assertSame($question, $tested);
    }

    private function question(bool $taskIsDone = false) : ChecklistQuestion
    {
        $question = new ChecklistQuestion();
        $question->setPossibleStatuses([
            [ 'done' => false ], [ 'done' => true ]
        ]);
        $question->setCurrentStatus($taskIsDone ? 1 : 0);
        $question->setResponsible(new Employee());
        $question->setLastPingDate(null);
        return $question;
    }

    private function message() : PingChecklistQuestion
    {
        return new PingChecklistQuestion(rand(0, 10000), new Employee());
    }

    private function handler(?ObjectRepository $repo = null, ?callable $pinger = null) : PingChecklistQuestionHandler
    {
        $repo ??= $this->mockObjectRepository(fn() => $this->question());
        $pinger ??= fn() => null;
        /** @var MockObject|EntityManagerInterface $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturnCallback(
            function (string $repoName) use ($repo) : ?ObjectRepository {
                switch ($repoName) {
                    case 'AdventureBundle:Checklist\ChecklistQuestion':
                    case ChecklistQuestion::class:
                        return $repo;
                    default:
                        return null;
                }
            }
        );
        /** @var MockObject|ChecklistQuestionPinger $pingerObj */
        $pingerObj = $this->getMockBuilder(ChecklistQuestionPinger::class)
            ->setMethods(['ping'])
            ->disableOriginalConstructor()
            ->getMock();
        $pingerObj->expects($this->any())->method('ping')->willReturnCallback($pinger);
        return new PingChecklistQuestionHandler($em, new TaskDoneFilter(), $pingerObj);
    }
}
