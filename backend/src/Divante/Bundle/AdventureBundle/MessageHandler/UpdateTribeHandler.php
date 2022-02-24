<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:57
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\UpdateTribe;
use Divante\Bundle\AdventureBundle\Entity\TribeRotationHistory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateTribeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateTribe $message
     * @throws Exception
     */
    public function __invoke(UpdateTribe $message) : void
    {
        $em = $this->em;
        $tribe = $message->getEntry();
        $em->getConnection()->beginTransaction();
        try {
            if ($message->getName() !== $tribe->getName()) {
                /** @var TribeRotationHistory[] $tribeRotationHistory */
                $tribeRotationHistory = $em->getRepository(TribeRotationHistory::class)
                    ->findBy(['tribeName' => $tribe->getName()]);
                foreach ($tribeRotationHistory as $tribeName) {
                    $tribeName->setTribeName($message->getName());
                    $em->persist($tribeName);
                }
            }
            $techLeaderId = $message->getTechLeaderId();
            /** @var Employee|null $currentTechLeader */
            $currentTechLeader = $tribe->getTechLeader();
            if ($techLeaderId !== null) {
                if ($currentTechLeader !== null && $currentTechLeader->getId() !== $techLeaderId) {
                    $currentTechLeader->setTechTribeLeader(false);
                    $em->persist($currentTechLeader);
                }
                $currentTechLeader = $em->getRepository(Employee::class)->find($techLeaderId);
                if (!$currentTechLeader->isTechTribeLeader()) {
                    $currentTechLeader->setTechTribeLeader(true);
                    $em->persist($currentTechLeader);
                }
            }
            if ($techLeaderId === null && $currentTechLeader !== null) {
                $currentTechLeader->setTechTribeLeader(false);
                $em->persist($currentTechLeader);
            }
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
                ->setTechLeader($currentTechLeader)
                ->setUpdatedAt();
            $tribe->getResponsible()->clear();
            $responsible = $message->getResponsible();
            foreach ($responsible as $responsibleId) {
                /** @var Employee|null $employee */
                $employee = $this->em->getRepository(Employee::class)->find($responsibleId);
                if (is_null($employee)) {
                    throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $responsibleId));
                }
                $tribe->getResponsible()->add($employee);
            }
            $em->persist($tribe);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw new Exception("Updating tribe failed", 0, $e);
        }
    }
}
