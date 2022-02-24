<?php

namespace Tests\AdventureBundle\Messages\Handler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\MessageHandler\Checklist\EditChecklistTemplateQuestionHandler;
use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Services\PossibleStatusesVerificator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class EditChecklistTemplateQuestionHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;
    
    public function testLookingForCorrectQuestion() : void
    {
        /** @var int|null $foundQuestionId */
        $foundQuestionId = null;
        $find = function (int $id) use (&$foundQuestionId) {
            $foundQuestionId = $id;
            $question = new ChecklistTemplateQuestion();
            $question
                ->setChecklist((new ChecklistTemplate())
                ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
                ->setResponsible(null);
            return $question;
        };
        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository($find),
            )
        );

        $message = $this->prepareMessage();
        $handler($message);
        $this->assertNotNull($foundQuestionId);
        $this->assertEquals($message->getQuestionId(), $foundQuestionId);
    }

    public function testExceptionOnQuestionNotFound() : void
    {
        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => null),
            )
        );

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*question.*not.*found.*/i');
        $message = $this->prepareMessage();
        $handler($message);
    }

    public function testLookingForCorrectEmployee() : void
    {
        /** @var int|null $foundEmployeeId */
        $foundEmployeeId = null;
        $find = function (int $id) use (&$foundEmployeeId) {
            $foundEmployeeId = $id;
            return new Employee();
        };
        $question = new ChecklistTemplateQuestion();
        $question->setChecklist((new ChecklistTemplate())->setType(ChecklistTemplate::TYPE_DISTRIBUTED));
        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
                $this->mockObjectRepository($find),
            ),
        );

        $message = $this->prepareMessage(false);
        $handler($message);
        $this->assertNotNull($foundEmployeeId);
        $this->assertEquals($message->getResponsibleId(), $foundEmployeeId);
    }

    public function testExceptionOnEmployeeNotFound() : void
    {
        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                null,
                $this->mockObjectRepository(fn() => null),
            ),
        );

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessageMatches('/.*employee.*not.*found.*/i');
        $message = $this->prepareMessage(false);
        $handler($message);
    }

    public function testFindNotCalledWhenEmployeeNotSet() : void
    {
        $findCalled = false;
        $find = function () use (&$findCalled) {
            $findCalled = true;
            return new Employee();
        };
        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                null,
                $this->mockObjectRepository($find),
            ),
        );

        $message = $this->prepareMessage(true);
        $handler($message);
        $this->assertFalse($findCalled);
    }

    public function testValidatorCalledWithStatuses() : void
    {
        /** @var array|null $statuses */
        $statuses = null;
        $validate = function (array $passedStatuses) use (&$statuses) {
            $statuses = $passedStatuses;
        };
        $handler = $this->prepareHandler(
            null,
            $validate
        );

        $message = $this->prepareMessage();
        $handler($message);
        $this->assertNotNull($statuses);
        $this->assertEquals($message->getPossibleStatuses(), $statuses);
    }

    public function testExceptionWhenValidationFailed() : void
    {
        $exception = "RandomExceptionMessage".rand(0, 10000);
        $validator = function () use ($exception) {
            throw new Exception($exception);
        };

        $handler = $this->prepareHandler(
            null,
            $validator,
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage($exception);
        $message = $this->prepareMessage();
        $handler($message);
    }

    public function testValidatorNotCalledWhenStatusesNotPassed() : void
    {
        $validatorCalled = false;
        $validate = function () use (&$validatorCalled) {
            $validatorCalled = true;
        };
        $handler = $this->prepareHandler(
            null,
            $validate
        );

        $message = $this->prepareMessage(null, true);
        $handler($message);
        $this->assertFalse($validatorCalled);
    }

    public function testExceptionOnResponsibleEmployeePassedWhenTemplateUnited() : void
    {
        $template = new ChecklistTemplate();
        $template->setType(ChecklistTemplate::TYPE_UNITED);
        $question = new ChecklistTemplateQuestion();
        $question->setChecklist($template);
        $employee = new Employee();

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
                $this->mockObjectRepository(fn() => $employee),
            ),
        );
        $message = $this->prepareMessage(false);
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches("/.*employee.*passed.*template.*united.*/i");
        $handler($message);
    }

    public function testNamePlUpdated() : void
    {
        /** @var ChecklistTemplateQuestion|MockObject $question */
        $question = $this->getMockBuilder(ChecklistTemplateQuestion::class)
            ->disableOriginalConstructor()
            ->setMethods(['setNamePl'])
            ->getMock();
        /** @var string|null $settedValue */
        $settedValue = null;
        $question->expects($this->any())->method('setNamePl')->willReturnCallback(
            function (string $val) use (&$settedValue, $question) {
                $settedValue = $val;
                return $question;
            }
        );
        $question
            ->setChecklist((new ChecklistTemplate())
            ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
            ->setResponsible(null);

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
            ),
        );
        $message = $this->prepareMessage();
        $handler($message);

        $this->assertNotNull($settedValue);
        $this->assertEquals($message->getNamePl(), $settedValue);
    }

    public function testNameEnUpdated() : void
    {
        /** @var ChecklistTemplateQuestion|MockObject $question */
        $question = $this->getMockBuilder(ChecklistTemplateQuestion::class)
            ->disableOriginalConstructor()
            ->setMethods(['setNameEn'])
            ->getMock();
        /** @var string|null $settedValue */
        $settedValue = null;
        $question->expects($this->any())->method('setNameEn')->willReturnCallback(
            function (string $val) use (&$settedValue, $question) {
                $settedValue = $val;
                return $question;
            }
        );
        $question
            ->setChecklist((new ChecklistTemplate())
            ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
            ->setResponsible(null);

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
            ),
        );
        $message = $this->prepareMessage();
        $handler($message);

        $this->assertNotNull($settedValue);
        $this->assertEquals($message->getNameEn(), $settedValue);
    }

    public function testDescriptionPlUpdated() : void
    {
        /** @var ChecklistTemplateQuestion|MockObject $question */
        $question = $this->getMockBuilder(ChecklistTemplateQuestion::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDescriptionPl'])
            ->getMock();
        /** @var string|null $settedValue */
        $settedValue = null;
        $question->expects($this->any())->method('setDescriptionPl')->willReturnCallback(
            function (string $val) use (&$settedValue, $question) {
                $settedValue = $val;
                return $question;
            }
        );
        $question
            ->setChecklist((new ChecklistTemplate())
            ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
            ->setResponsible(null);

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
            ),
        );
        $message = $this->prepareMessage();
        $handler($message);

        $this->assertNotNull($settedValue);
        $this->assertEquals($message->getDescriptionPl(), $settedValue);
    }

    public function testDescriptionEnUpdated() : void
    {
        /** @var ChecklistTemplateQuestion|MockObject $question */
        $question = $this->getMockBuilder(ChecklistTemplateQuestion::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDescriptionEn'])
            ->getMock();
        /** @var string|null $settedValue */
        $settedValue = null;
        $question->expects($this->any())->method('setDescriptionEn')->willReturnCallback(
            function (string $val) use (&$settedValue, $question) {
                $settedValue = $val;
                return $question;
            }
        );
        $question
            ->setChecklist((new ChecklistTemplate())
            ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
            ->setResponsible(null);

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
            ),
        );
        $message = $this->prepareMessage();
        $handler($message);

        $this->assertNotNull($settedValue);
        $this->assertEquals($message->getDescriptionEn(), $settedValue);
    }

    public function testPossibleStatusesUpdated() : void
    {
        /** @var ChecklistTemplateQuestion|MockObject $question */
        $question = $this->getMockBuilder(ChecklistTemplateQuestion::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPossibleStatuses'])
            ->getMock();
        /** @var array|null $settedValue */
        $settedValue = null;
        $question->expects($this->any())->method('setPossibleStatuses')->willReturnCallback(
            function (array $val) use (&$settedValue, $question) {
                $settedValue = $val;
                return $question;
            }
        );
        $question
            ->setChecklist((new ChecklistTemplate())
            ->setType(ChecklistTemplate::TYPE_DISTRIBUTED))
            ->setResponsible(null);

        $handler = $this->prepareHandler(
            $this->prepareEntityManagerInterface(
                $this->mockObjectRepository(fn() => $question),
            ),
        );
        $message = $this->prepareMessage();
        $handler($message);

        $this->assertNotNull($settedValue);
        $this->assertEquals($message->getPossibleStatuses(), $settedValue);
    }

    private function prepareMessage(
        ?bool $employeeIsNull = null,
        ?bool $statusesAreNull = null
    ) : EditChecklistTemplateQuestion {

        return new EditChecklistTemplateQuestion(
            rand(0, 10000),
            rand(0, 10000),
            "RandomNamePl".rand(0, 10000),
            "RandomNameEn".rand(0, 10000),
            "RandomDescPl".rand(0, 10000),
            "RandomDescEn".rand(0, 10000),
            ($statusesAreNull ?? false) ? null : $this->generateStatuses(),
            ($employeeIsNull ?? true) ? null : rand(0, 10000),
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
    
    private function prepareEntityManagerInterface(
        ?ObjectRepository $questionRepository = null,
        ?ObjectRepository $employeeRepository = null
    ) : EntityManagerInterface {
        if (is_null($questionRepository)) {
            $questionRepository = $this->mockObjectRepository(
                function () {
                    $template = new ChecklistTemplate();
                    $template->setType(ChecklistTemplate::TYPE_UNITED);
                    $question = new ChecklistTemplateQuestion();
                    $question->setChecklist($template);
                    $question->setPossibleStatuses([]);
                    $question->setResponsible(null);
                    return $question;
                }
            );
        }
        if (is_null($employeeRepository)) {
            $employeeRepository = $this->mockObjectRepository(fn() => new Employee());
        }
        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturnCallback(
            function (string $repoName) use ($questionRepository, $employeeRepository) {
                if (strpos($repoName, 'Employee') !== false) {
                    return $employeeRepository;
                } elseif (strpos($repoName, 'ChecklistTemplateQuestion') !== false) {
                    return $questionRepository;
                } else {
                    return null;
                }
            }
        );
        return $em;
    }

    private function prepareHandler(
        ?EntityManagerInterface $em = null,
        ?callable $validatorCallback = null
    ) : EditChecklistTemplateQuestionHandler {
        if (is_null($em)) {
            $em = $this->prepareEntityManagerInterface();
        }
        if (is_null($validatorCallback)) {
            $validatorCallback = fn() => null;
        }
        /** @var PossibleStatusesVerificator|MockObject $validator */
        $validator = $this->getMockBuilder(PossibleStatusesVerificator::class)
            ->disableOriginalConstructor()
            ->setMethods(['verify'])
            ->getMock();
        $validator->expects($this->any())->method('verify')->willReturnCallback($validatorCallback);
        return new EditChecklistTemplateQuestionHandler($em, $validator);
    }
}
