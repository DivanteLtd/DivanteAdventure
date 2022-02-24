<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:57
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Events\ProjectOccupancyEvent;
use Divante\Bundle\AdventureBundle\Message\Project\UpdateProject;
use Divante\Bundle\AdventureBundle\Services\IntegrationPairingsUpdate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateProjectHandler
{
    private EntityManagerInterface $em;
    private IntegrationPairingsUpdate $updatePairingsService;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        IntegrationPairingsUpdate $update,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->updatePairingsService = $update;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UpdateProject $message
     * @throws \Exception
     */
    public function __invoke(UpdateProject $message) : void
    {
        $project = $message->getEntry();
        $this->em->getConnection()->beginTransaction();
        try {
            $project
                ->setName($message->getName())
                ->setCode($message->getCode())
                ->setDescription($message->getDescription())
                ->setPhoto($message->getPhoto())
                ->setUrl($message->getUrl())
                ->setType($message->getType())
                ->setPlannedBudget($message->getBudget())
                ->setStartedAt($message->getStartedAt())
                ->setEndedAt($message->getEndedAt())
                ->setBillable($message->isBillable())
                ->setUpdatedAt();

            $this->updatePairingsService->update($project, $message);
            $this->em->persist($project);
            $this->em->flush();
            $this->em->getConnection()->commit();

            $this->dispatcher->dispatch(new ProjectOccupancyEvent($project));
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new \Exception("Updating project failed with error '".$e->getMessage()."'", 0, $e);
        }
    }
}
