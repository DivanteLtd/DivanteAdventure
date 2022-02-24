<?php

namespace Tests\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\MessageHandler\FAQ\CreateFAQQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQQuestion;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\EmployeeUserSuperAdmin;
use Tests\Utils\ObjectRepositoryMocker;

class CreateFAQQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testNotFound() : void
    {
        $repo = $this->mockObjectRepository(fn() => null);
        $em = $this->buildManagerMock($repo);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches("/.*not.*found.*/i");
        $handler($message);
    }

    public function testQuestionCreatedCorrectly() : void
    {
        /** @var FAQQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (FAQQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };

        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertNotNull($createdEntity);
        $this->assertInstanceOf(FAQQuestion::class, $createdEntity);
    }

    public function testCorrectQuestionPlSet() : void
    {
        /** @var FAQQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (FAQQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getQuestionPl(), $createdEntity->getQuestionPl());
    }

    public function testCorrectQuestionEnSet() : void
    {
        /** @var FAQQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (FAQQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getQuestionEn(), $createdEntity->getQuestionEn());
    }

    public function testCorrectAnswerPlSet() : void
    {
        /** @var FAQQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (FAQQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getAnswerPl(), $createdEntity->getAnswerPl());
    }

    public function testCorrectAnswerEnSet() : void
    {
        /** @var FAQQuestion|null $createdEntity */
        $createdEntity = null;
        $persistCallback = function (FAQQuestion $persisted) use (&$createdEntity) {
            $createdEntity = $persisted;
        };
        $employeeRepo = $this->mockObjectRepository(fn() => EmployeeUserSuperAdmin::getEmployee());
        $em = $this->buildManagerMock(null, $employeeRepo, $persistCallback);
        $handler = new CreateFAQQuestionHandler($em);
        $message = $this->generateMessage();
        $handler($message);

        $this->assertEquals($message->getAnswerEn(), $createdEntity->getAnswerEn());
    }

    private function generateMessage() : CreateFAQQuestion
    {
        $randomEmployeeId = rand(0, 10000);
        $randomCategoryId = rand(0, 10000);
        $randomQuestionPl = 'RandomQuestionPl'.rand(0, 10000);
        $randomQuestionEn = 'RandomQuestionEn '.rand(0, 10000);
        $randomAnswerPl = 'RandomAnswerPl '.rand(0, 10000);
        $randomAnswerEn = 'RandomAnswerEn '.rand(0, 10000);
        return new CreateFAQQuestion(
            $randomEmployeeId,
            $randomCategoryId,
            $randomQuestionPl,
            $randomQuestionEn,
            $randomAnswerPl,
            $randomAnswerEn
        );
    }

    private function buildManagerMock(
        ?ObjectRepository $fAQCategoryRepo,
        ?ObjectRepository $employeeRepo = null,
        ?callable $persistCallback = null
    ) : EntityManagerInterface {
        if (is_null($fAQCategoryRepo)) {
            $template = new FAQCategory();
            $fAQCategoryRepo = $this->mockObjectRepository(fn() => $template);
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
            function (string $repoName) use ($fAQCategoryRepo, $employeeRepo) {
                if (strpos($repoName, 'Employee') !== false) {
                    return $employeeRepo;
                } elseif (strpos($repoName, 'FAQCategory') !== false) {
                    return $fAQCategoryRepo;
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
}
