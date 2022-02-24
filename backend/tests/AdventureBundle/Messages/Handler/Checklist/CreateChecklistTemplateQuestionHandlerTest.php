<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\CreateChecklistTemplateQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Services\PossibleStatusesVerificator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class CreateChecklistTemplateQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testTemplateNotFound() : void
    {
         $repo = $this->mockObjectRepository(fn() => null);
         $em = $this->buildManagerMock($repo);
         $handler = $this->getHandler($em);
         $message = $this->generateMessage();

         $this->expectException(NotFoundHttpException::class);
         $this->expectExceptionMessageMatches("/.*template.*not.*found.*/i");
         $handler($message);
    }

    public function testTemplateLookedForCorrectly() : void
    {
        $template = new ChecklistTemplate();
        $template->setType(ChecklistTemplate::TYPE_UNITED);
        /** @var int|null $usedId */
        $usedId = null;
        $repo = $this->mockObjectRepository(
            function (int $id) use (&$usedId, $template) {
                $usedId = $id;
                return $template;
            }
        );

        $em = $this->buildManagerMock($repo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage(true);

        $handler($message);
        $this->assertNotNull($usedId);
        $this->assertEquals($message->getTemplateId(), $usedId);
    }

    public function testOnNullResponsibleNoSearch() : void
    {
        $template = new ChecklistTemplate();
        $template->setType(ChecklistTemplate::TYPE_UNITED);
        $templateRepo = $this->mockObjectRepository(fn() => $template);

        $called = false;
        $employeeRepo = $this->mockObjectRepository(
            function () use (&$called) {
                $called = true;
                return null;
            }
        );

        $em = $this->buildManagerMock($templateRepo, $employeeRepo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage(true);

        $handler($message);
        $this->assertFalse($called);
    }

    public function testOnResponsibleSearchesForEmployee() : void
    {
        /** @var int|null $employeeId */
        $employeeId = null;
        $employeeRepo = $this->mockObjectRepository(
            function (int $id) use (&$employeeId) {
                $employeeId = $id;
                return new Employee();
            }
        );

        $em = $this->buildManagerMock(null, $employeeRepo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();

        $handler($message);
        $this->assertNotNull($employeeId);
        $this->assertEquals($message->getResponsibleId(), $employeeId);
    }

    public function testEmployeeWithGivenIdNotFound() : void
    {
        $employeeRepo = $this->mockObjectRepository(fn() => null);
        $em = $this->buildManagerMock(null, $employeeRepo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches("/.*employee.*not.*found.*/i");
        $handler($message);
    }

    public function testEmployeePassedOnUnitedTemplateThrowException() : void
    {
        $template = new ChecklistTemplate();
        $template->setType(ChecklistTemplate::TYPE_UNITED);
        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $templateRepo = $this->mockObjectRepository(fn() => $template);
        $em = $this->buildManagerMock($templateRepo, $employeeRepo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*employee.*passed.*template.*united.*/i");
        $handler($message);
    }

    public function testEmployeeNotPassedOnDistributedTemplateThrowException() : void
    {
        $template = new ChecklistTemplate();
        $template->setType(ChecklistTemplate::TYPE_DISTRIBUTED);
        $employeeRepo = $this->mockObjectRepository(fn() => null);
        $templateRepo = $this->mockObjectRepository(fn() => $template);
        $em = $this->buildManagerMock($templateRepo, $employeeRepo);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage(true);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*employee.*not.*passed.*template.*distributed.*/i");
        $handler($message);
    }

    public function testStatusesPassedToVerificator() : void
    {
        /** @var array|null $verifiedStatuses */
        $verifiedStatuses = null;
        $verificatorCallback = function (array $data) use (&$verifiedStatuses) {
            $verifiedStatuses = $data;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo);
        $handler = $this->getHandler($em, $verificatorCallback);
        $message = $this->generateMessage();

        $handler($message);
        $this->assertNotNull($verifiedStatuses);
        $this->assertEquals($message->getPossibleStatuses(), $verifiedStatuses);
    }

    public function testStatusesThrowException() : void
    {
        $thrownException = new \Exception("RandomExceptionMessage".rand(1, 100000));
        $verificatorCallback = function () use ($thrownException) {
            throw $thrownException;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo);
        $handler = $this->getHandler($em, $verificatorCallback);
        $message = $this->generateMessage();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage($thrownException->getMessage());
        $handler($message);
    }

    public function testQuestionCreatedCorrectly() : void
    {
        /** @var ChecklistTemplateQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertNotNull($createdEntity);
        $this->assertInstanceOf(ChecklistTemplateQuestion::class, $createdEntity);
    }

    public function testQuestionHasCorrectChecklist() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $checklist = new ChecklistTemplate();
        $checklist->setType(ChecklistTemplate::TYPE_DISTRIBUTED);
        $checklistRepo = $this->mockObjectRepository(fn() => $checklist);
        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock($checklistRepo, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($checklist, $createdEntity->getChecklist());
    }

    public function testQuestionHasCorrectResponsibleEmployee() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $employee = new Employee();
        $employeeRepo = $this->mockObjectRepository(fn() => $employee);
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($employee, $createdEntity->getResponsible());
    }

    public function testQuestionHasCorrectPossibleStatuses() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getPossibleStatuses(), $createdEntity->getPossibleStatuses());
    }

    public function testQuestionHasCorrectDescriptionEn() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getDescriptionEn(), $createdEntity->getDescriptionEn());
    }

    public function testQuestionHasCorrectDescriptionPl() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getDescriptionPl(), $createdEntity->getDescriptionPl());
    }

    public function testQuestionHasCorrectNameEn() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getNameEn(), $createdEntity->getNameEn());
    }

    public function testQuestionHasCorrectNamePl() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getNamePl(), $createdEntity->getNamePl());
    }

    public function testQuestionHasCorrectCreatedAt() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEqualsWithDelta(new \DateTime(), $createdEntity->getCreatedAt(), 5);
    }

    public function testQuestionHasCorrectUpdatedAt() : void
    {
        /** @var ChecklistTemplateQuestion $createdEntity */
        $createdEntity = null;
        $persistCallback = function (ChecklistTemplateQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => new Employee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = $this->getHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEqualsWithDelta(new \DateTime(), $createdEntity->getUpdatedAt(), 5);
    }

    private function generateMessage(
        bool $responsibleNull = false,
        ?array $statuses = null
    ) : CreateChecklistTemplateQuestion {
        return new CreateChecklistTemplateQuestion(
            rand(0, 10000),
            "RandomNamePl" . rand(0, 10000),
            "RandomNameEn" . rand(0, 10000),
            "RandomDescPl" . rand(0, 10000),
            "RandomDescEn" . rand(0, 10000),
            $statuses ?? $this->generateStatuses(),
            $responsibleNull ? null : rand(0, 10000),
        );
    }

    private function generateStatuses() : array
    {
        $count = rand(1, 10000);
        $result = [];
        for ($i = 0; $i < $count; $i++) {
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

    private function buildManagerMock(
        ?ObjectRepository $templateRepo,
        ?ObjectRepository $employeeRepo = null,
        ?callable $persistCallback = null
    ) : EntityManagerInterface {
        if (is_null($templateRepo)) {
            $template = new ChecklistTemplate();
            $template->setType(ChecklistTemplate::TYPE_DISTRIBUTED);
            $templateRepo = $this->mockObjectRepository(fn() => $template);
        }
        if (is_null($employeeRepo)) {
            $employeeRepo = $this->mockObjectRepository(fn() => null);
        }

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'persist'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturnCallback(
            function (string $repoName) use ($templateRepo, $employeeRepo) {
                if (strpos($repoName, 'Employee') !== false) {
                    return $employeeRepo;
                } elseif (strpos($repoName, 'Template') !== false) {
                    return $templateRepo;
                } else {
                    return null;
                }
            }
        );
        if (!is_null($persistCallback)) {
            $em->expects($this->any())->method('persist')->willReturnCallback($persistCallback);
        }
        return $em;
    }

    private function getHandler(
        EntityManagerInterface $em,
        ?callable $verifactorCallable = null
    ) : CreateChecklistTemplateQuestionHandler {
        /** @var PossibleStatusesVerificator|MockObject $verificator */
        $verificator = $this->getMockBuilder(PossibleStatusesVerificator::class)
            ->disableOriginalConstructor()
            ->setMethods(['verify'])
            ->getMock();
        if (is_null($verifactorCallable)) {
            $verificator->expects($this->any())->method('verify')->willReturnSelf();
        } else {
            $verificator->expects($this->any())->method('verify')->willReturnCallback($verifactorCallable);
        }
        return new CreateChecklistTemplateQuestionHandler($em, $verificator);
    }
}
