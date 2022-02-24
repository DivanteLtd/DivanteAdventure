<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Message\Positions\DeleteLevel;
use Doctrine\ORM\EntityManagerInterface;

class DeleteLevelHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteLevel $message
     * @throws \Exception
     */
    public function __invoke(DeleteLevel $message) : void
    {
        $level = $this->getLevelToDelete($message->getDeletedLevelId());
        if (!$level->getEmployees()->isEmpty()) {
            throw new \Exception("Given level is not empty");
        }

        $em = $this->em;
        $em->beginTransaction();
        try {
            $em->remove($level);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    /**
     * @param int $levelId
     * @return Level
     * @throws \Exception
     */
    public function getLevelToDelete(int $levelId) : Level
    {
        $em = $this->em;
        $repo = $em->getRepository(Level::class);
        /** @var Level|null $level */
        $level = $repo->find($levelId);
        if (is_null($level)) {
            throw new \Exception("Level with ID $levelId has not been found");
        }
        return $level;
    }
}
