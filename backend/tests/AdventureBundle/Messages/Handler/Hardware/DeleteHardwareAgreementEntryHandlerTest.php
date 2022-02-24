<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */


namespace Tests\AdventureBundle\Messages\Handler\Hardware;


use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\MessageHandler\Hardware\DeleteHardwareAgreementEntryHandler;
use Divante\Bundle\AdventureBundle\Message\Hardware\DeleteHardwareAgreementEntry;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\FoundationTestCase;
use Tests\Utils\ObjectRepositoryMocker;

class DeleteHardwareAgreementEntryHandlerTest extends FoundationTestCase
{
    use ObjectRepositoryMocker;

    public function testIdPassedToFinder() : void
    {
        $message = new DeleteHardwareAgreementEntry(rand(0, 10000));
        /** @var int|null $passedId */
        $passedId = null;
        $handler = $this->getHandler(
            function (int $id) use (&$passedId) {
                $passedId = $id;
                return new HardwareAgreement();
            }
        );
        $handler($message);
        $this->assertEquals($message->getId(), $passedId);
    }

    public function testIdNotFound() : void
    {
        $message = new DeleteHardwareAgreementEntry(rand(0, 10000));
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
        $message = new DeleteHardwareAgreementEntry(rand(0, 10000));
        $obj = new HardwareAgreement();
        /** @var null|HardwareAgreement $deleted */
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
        $this->assertInstanceOf(HardwareAgreement::class, $deleted);
        $this->assertSame($obj, $deleted);
    }

    private function getHandler(callable $find, ?callable $remove = null) : DeleteHardwareAgreementEntryHandler
    {
        return new DeleteHardwareAgreementEntryHandler(
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
        $em->expects($this->any())->method('getRepository')->willReturn($repo);
        if (!is_null($removeCallback)) {
            $em->expects($this->any())->method('remove')->willReturnCallback($removeCallback);
        }
        return $em;
    }
}