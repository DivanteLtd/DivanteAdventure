<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.03.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Message\CreateTribe;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateTribeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateTribe $message
     * @throws Exception
     */
    public function __invoke(CreateTribe $message) : void
    {
        $em = $this->em;

        $em->getConnection()->beginTransaction();
        try {
            $techLeader = null;
            if ($message->getTechLeaderId() !== null) {
                $techLeader = $em->getRepository(Employee::class)->find($message->getTechLeaderId());
            }
            $tribe = new Tribe();
            $tribe
                ->setName($message->getName())
                ->setDescription($message->getDescription())
                ->setPhoto($message->getPhotoUrl())
                ->setUrl($message->getUrl())
                ->setVirtual($message->isVirtual())
                ->setHrEmail($message->getHrEmail())
                ->setFreeDayProjectId($message->getFreeDayProjectId())
                ->setFreeDayCategoryId($message->getFreeDayCategoryId())
                ->setSickLeaveProjectId($message->getSickLeaveProjectId())
                ->setSickLeaveCategoryId($message->getSickLeaveCategoryId())
                ->setTechLeader($techLeader)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($tribe);
            $responsible = $message->getResponsible();
            foreach ($responsible as $responsibleId) {
                /** @var Employee|null $employee */
                $employee = $this->em->getRepository(Employee::class)->find($responsibleId);
                if (is_null($employee)) {
                    throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $responsibleId));
                }
                $tribe->getResponsible()->add($employee);
            }
            $this->em->persist($tribe);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }
}
