<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\Checklist\ChecklistAssignedEvent;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\ApplyChecklistTemplateHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\ApplyChecklistTemplate;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class ApplyChecklistTemplateHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testFindTemplateCalled() : void
    {
        /** @var int|null $foundId */
        $foundId = null;
        $findCallback = function (int $id) use (&$foundId) : ?ChecklistTemplate {
            $foundId = $id;
            return $this->generateChecklistTemplate();
        };
        $handler = $this->getHandler($findCallback);
        $templateId = rand(1, 10000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            $templateId,
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertNotNull($foundId);
        $this->assertEquals($templateId, $foundId);
        $this->assertEquals($hidden, $message->isHidden());
    }

    public function testExceptionOnTemplateNotFound() : void
    {
        $findCallback = fn() => null;
        $handler = $this->getHandler($findCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*template.*not.*found.*/i');
        $handler($message);
    }

    public function testFindEmployeeCalled() : void
    {
        /** @var int[]] $foundIds */
        $foundIds = [];
        $findCallback = function (int $id) use (&$foundIds) : ?Employee {
            $foundIds[] = $id;
            return $this->generateEmployee();
        };
        $handler = $this->getHandler(null, $findCallback);
        $subjectId = rand(10001, 20000);
        $userId = rand(20001, 30000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(rand(1, 10000), [], $subjectId, $hidden, $userId, '2020-11-18');
        $handler($message);
        $this->assertCount(2, $foundIds);
        $this->assertContains($subjectId, $foundIds);
        $this->assertContains($userId, $foundIds);
    }

    public function testExceptionOnSubjectNotFound() : void
    {
        $findCallback = fn() => null;
        $handler = $this->getHandler(null, $findCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*subject.*not.*found.*/i');
        $handler($message);
    }

    public function testFindOwnerCalled() : void
    {
        /** @var int[]] $foundIds */
        $foundIds = [];
        $findCallback = function (int $id) use (&$foundIds) : ?Employee {
            $foundIds[] = $id;
            return $this->generateEmployee();
        };
        $handler = $this->getHandler(null, $findCallback);
        $ownerId = rand(1, 10000);
        $subjectId = rand(10001, 20000);

        $this->expectException(NotFoundHttpException::class);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [$ownerId],
            $subjectId,
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $this->assertCount(2, $foundIds);
        $this->assertContains($ownerId, $foundIds);
        $this->assertContains($subjectId, $foundIds);
    }

    public function testExceptionOnOwnerNotFound() : void
    {
        $ownerId = rand(1, 10000);
        $subjectId = rand(10001, 20000);

        $findCallback = fn(int $id) => $id === $ownerId ? null : $this->generateEmployee();
        $handler = $this->getHandler(null, $findCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [$ownerId],
            $subjectId,
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*owner.*not.*found.*/i');
        $handler($message);
    }

    public function testChecklistPersisted() : void
    {
        /** @var Checklist|null $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);

        $this->assertNotNull($persistedChecklist);
        $this->assertInstanceOf(Checklist::class, $persistedChecklist);
    }

    public function testChecklistHasCorrectType() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $template = $this->generateChecklistTemplate();
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertEquals($template->getType(), $persistedChecklist->getType());
    }

    public function testChecklistHasCorrectNamePl() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $template = $this->generateChecklistTemplate();
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertEquals($template->getNamePl(), $persistedChecklist->getNamePl());
    }

    public function testChecklistHasCorrectNamePlReplacesOwner() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $owner = $this->generateEmployee();
        $ownerId = rand(1, 10000);
        $namePlTemplate = "RandomNamePl %OWNER% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNamePl($namePlTemplate);
        $handler = $this->getHandler(
            fn() => $template,
            fn(int $id) => $id === $ownerId ? $owner : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $expectedResult = str_replace('%OWNER%', $owner->getName().' '.$owner->getLastName(), $namePlTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNamePl());
    }

    public function testChecklistHasCorrectNamePlReplacesOwnerNull() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $namePlTemplate = "RandomNamePl %OWNER% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNamePl($namePlTemplate);
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        $expectedResult = str_replace('%OWNER%', '', $namePlTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNamePl());
    }

    public function testChecklistHasCorrectNamePlReplacesSubject() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $subject = $this->generateEmployee();
        $subjectId = rand(1, 10000);
        $namePlTemplate = "RandomNamePl %SUBJECT% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNamePl($namePlTemplate);
        $handler = $this->getHandler(
            fn() => $template,
            fn(int $id) => $id === $subjectId ? $subject : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $expectedResult = str_replace('%SUBJECT%', $subject->getName().' '.$subject->getLastName(), $namePlTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNamePl());
    }

    public function testChecklistHasCorrectNameEn() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $template = $this->generateChecklistTemplate();
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertEquals($template->getNameEn(), $persistedChecklist->getNameEn());
    }

    public function testChecklistHasCorrectNameEnReplacesOwner() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $owner = $this->generateEmployee();
        $ownerId = rand(1, 10000);
        $nameEnTemplate = "RandomNameEn %OWNER% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNameEn($nameEnTemplate);
        $handler = $this->getHandler(
            fn() => $template,
            fn(int $id) => $id === $ownerId ? $owner : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);$message = new ApplyChecklistTemplate(
        rand(1, 10000),
        [$ownerId],
        rand(10001, 20000),
        $hidden,
        rand(1, 10000),
        '2020-11-18'
    );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $expectedResult = str_replace('%OWNER%', $owner->getName().' '.$owner->getLastName(), $nameEnTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNameEn());
    }

    public function testChecklistHasCorrectNameEnReplacesOwnerNull() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $nameEnTemplate = "RandomNameEn %OWNER% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNameEn($nameEnTemplate);
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $expectedResult = str_replace('%OWNER%', '', $nameEnTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNameEn());
    }

    public function testChecklistHasCorrectNameEnReplacesSubject() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $subject = $this->generateEmployee();
        $subjectId = rand(1, 10000);
        $nameEnTemplate = "RandomNameEn %SUBJECT% ".rand(0, 10000);
        $template = $this->generateChecklistTemplate()->setNameEn($nameEnTemplate);
        $handler = $this->getHandler(
            fn() => $template,
            fn(int $id) => $id === $subjectId ? $subject : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(1, 10000)],
            $subjectId,
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $expectedResult = str_replace('%SUBJECT%', $subject->getName().' '.$subject->getLastName(), $nameEnTemplate);
        $this->assertEquals($expectedResult, $persistedChecklist->getNameEn());
    }

    public function testChecklistHasCorrectSubject() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $subject = $this->generateEmployee();
        $subjectId = rand(1, 10000);
        $handler = $this->getHandler(
            null,
            fn(int $id) => $id === $subjectId ? $subject : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [rand(10001, 20000)],
            $subjectId,
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertSame($subject, $persistedChecklist->getSubject());
    }

    public function testChecklistHasCorrectOwner() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $owner = $this->generateEmployee();
        $ownerId = rand(1, 10000);
        $handler = $this->getHandler(
            null,
            fn(int $id) => $id === $ownerId ? $owner : $this->generateEmployee(),
            $persistCallback,
        );
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [$ownerId],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );        $this->expectException(NotFoundHttpException::class);
        $handler($message);
        $this->assertSame($owner, $persistedChecklist->getOwners()->toArray()[0]);
    }

    public function testChecklistHasCorrectOwnerNull() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $this->assertEmpty($persistedChecklist->getOwners()->toArray());
    }

    public function testChecklistHasCorrectCreatedAtDate() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        $this->assertEqualsWithDelta(new DateTime(), $persistedChecklist->getCreatedAt(), 5);
    }

    public function testChecklistHasCorrectUpdatedAtDate() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $this->assertEqualsWithDelta(new DateTime(), $persistedChecklist->getUpdatedAt(), 5);
    }

    public function testChecklistHasCorrectStartedAtDate() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $this->assertEqualsWithDelta(new DateTime(), $persistedChecklist->getStartedAt(), 5);
    }

    public function testChecklistHasCorrectFinishedAtDate() : void
    {
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(null, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        $this->assertNull($persistedChecklist->getFinishedAt());
    }

    public function testChecklistQuestionsTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        $this->assertEquals($template->getQuestions()->count(), $persistedChecklist->getQuestions()->count());
    }

    public function testChecklistQuestionsNamePlTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($templateQuestions); $i++) {
            $this->assertEquals($templateQuestions[$i]->getNamePl(), $checklistQuestions[$i]->getNamePl());
        }
    }

    public function testChecklistQuestionsNameEnTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($templateQuestions); $i++) {
            $this->assertEquals($templateQuestions[$i]->getNameEn(), $checklistQuestions[$i]->getNameEn());
        }
    }

    public function testChecklistQuestionsDescriptionPlTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($templateQuestions); $i++) {
            $this->assertEquals(
                $templateQuestions[$i]->getDescriptionPl(),
                $checklistQuestions[$i]->getDescriptionPl()
            );
        }
    }

    public function testChecklistQuestionsDescriptionEnTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($templateQuestions); $i++) {
            $this->assertEquals(
                $templateQuestions[$i]->getDescriptionEn(),
                $checklistQuestions[$i]->getDescriptionEn()
            );
        }
    }

    public function testChecklistQuestionsPossibleStatusesTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($templateQuestions); $i++) {
            $this->assertEquals(
                $templateQuestions[$i]->getPossibleStatuses(),
                $checklistQuestions[$i]->getPossibleStatuses()
            );
        }
    }

    public function testChecklistQuestionsResponsibleTransfered() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        /** @var ChecklistTemplateQuestion[] $templateQuestions */
        $templateQuestions = $template->getQuestions()->toArray();
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($checklistQuestions); $i++) {
            $this->assertSame(
                $templateQuestions[$i]->getResponsible(),
                $checklistQuestions[$i]->getResponsible()
            );
        }
    }

    public function testChecklistQuestionsCreatedAtSet() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($checklistQuestions); $i++) {
            $this->assertEqualsWithDelta(new DateTime(), $checklistQuestions[$i]->getCreatedAt(), 5);
        }
    }

    public function testChecklistQuestionsUpdatedAtSet() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($checklistQuestions); $i++) {
            $this->assertEqualsWithDelta(new DateTime(), $checklistQuestions[$i]->getUpdatedAt(), 5);
        }
    }

    public function testChecklistQuestionsCheckedAtSet() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($checklistQuestions); $i++) {
            $this->assertNull($checklistQuestions[$i]->getCheckedAt());
        }
    }

    public function testChecklistQuestionsStatusSet() : void
    {
        $template = $this->generateChecklistTemplate();
        /** @var Checklist $persistedChecklist */
        $persistedChecklist = null;
        $persistCallback = function ($checklist) use (&$persistedChecklist) {
            if ($checklist instanceof Checklist) {
                $persistedChecklist = $checklist;
            }
        };
        $handler = $this->getHandler(fn() => $template, null, $persistCallback);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $handler($message);
        /** @var ChecklistQuestion[] $checklistQuestions */
        $checklistQuestions = $persistedChecklist->getQuestions()->toArray();
        for ($i = 0; $i < count($checklistQuestions); $i++) {
            $allStatuses = $checklistQuestions[$i]->getPossibleStatuses();
            $defaultStatus = -1;
            /**
             * @var int $status
             * @var array $data
             */
            foreach ($allStatuses as $status => $data) {
                if ($data['default'] ?? false) {
                    if ($defaultStatus === -1) {
                        $defaultStatus = $status;
                    } else {
                        $this->fail("There are more than one possible status with 'default' set to 'true'");
                    }
                }
            }
            $this->assertEquals($defaultStatus, $checklistQuestions[$i]->getCurrentStatus());
        }
    }

    public function testEventDispatched(): void
    {
        /** @var Event|ChecklistAssignedEvent|null $dispatchedEvent */
        $dispatchedEvent = null;
        $dispatchCallback = function (Event $event) use (&$dispatchedEvent) {
            $dispatchedEvent = $event;
        };

        $handler = $this->getHandler(null, null, null, $dispatchCallback);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            false,
            rand(1, 10000),
            '2020-11-18'
        );

        $handler($message);
        $this->assertInstanceOf(ChecklistAssignedEvent::class, $dispatchedEvent);
    }


    private function generateChecklistTemplate() : ChecklistTemplate
    {
        $template = (new ChecklistTemplate())
            ->setType(rand(0, 10000))
            ->setNamePl("RandomNamePl".rand(0, 10000))
            ->setNameEn("RandomNameEn".rand(0, 10000));
        for ($i = 0; $i < rand(3, 15); $i++) {
            $template->addQuestion($this->generateQuestion());
        }
        return $template;
    }

    private function generateQuestion() : ChecklistTemplateQuestion
    {
        $question = new ChecklistTemplateQuestion();
        $question->setNamePl("RandomNamePl".rand(0, 10000));
        $question->setNameEn("RandomNameEn".rand(0, 10000));
        $question->setDescriptionPl("RandomNamePl".rand(0, 10000));
        $question->setDescriptionEn("RandomNameEn".rand(0, 10000));
        $question->setResponsible(rand(0, 10000) % 2 === 0 ? null : $this->generateEmployee());
        $question->setPossibleStatuses($this->generateStatuses());
        return $question;
    }

    private function generateStatuses() : array
    {
        $result = [];
        for ($i = 0; $i < rand(3, 15); $i++) {
            $status = $this->generateStatus();
            if ($i === 0) {
                $status['default'] = true;
            }
            $result[] = $status;
        }
        return $result;
    }

    private function generateStatus() : array
    {
        return [
            'label_pl' => "RandomLabelPl".rand(0, 10000),
            'label_en' => "RandomLabelEn".rand(0, 10000),
            'icon' => "RandomIcon".rand(0, 10000),
            'color' => "RandomColor".rand(0, 10000),
            'done' => rand(0, 10) < 5,
        ];
    }

    private function generateEmployee() : Employee
    {
        return (new Employee())
            ->setName("RandomEmployeeName".rand(0, 10000))
            ->setLastName("RandomEmployeeLastName".rand(0, 10000));
    }

    private function getHandler(
        ?callable $findTemplateCallback = null,
        ?callable $findEmployeeCallback = null,
        ?callable $persistCallback = null,
        ?callable $eventDispatchCallback = null
    ) : ApplyChecklistTemplateHandler {
        $findTemplateCallback ??= fn() => $this->generateChecklistTemplate();
        $findEmployeeCallback ??= fn() => $this->generateEmployee();
        $persistCallback ??= fn() => null;
        $eventDispatchCallback ??= fn() => null;

        $templateRepo = $this->mockObjectRepository($findTemplateCallback);
        $employeeRepo = $this->mockObjectRepository($findEmployeeCallback);

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'getRepository'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('persist')->willReturnCallback($persistCallback);
        $em->expects($this->any())->method('getRepository')->willReturnCallback(
            function (string $repoName) use ($employeeRepo, $templateRepo) : ?ObjectRepository {
                if (strpos($repoName, 'Employee') !== false) {
                    return $employeeRepo;
                } elseif (strpos($repoName, 'Checklist') !== false) {
                    return $templateRepo;
                } else {
                    return null;
                }
            }
        );

        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMockForAbstractClass();
        $eventDispatcher->expects($this->any())->method('dispatch')->willReturnCallback($eventDispatchCallback);

        return new ApplyChecklistTemplateHandler($em, $eventDispatcher);
    }
}
