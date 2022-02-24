<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeletePost;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeletePostHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param DeletePost $deletePost
     * @throws Exception
     */
    public function __invoke(DeletePost $deletePost) : void
    {
        $em = $this->em;
        /** @var News $news */
        $news = $em->getRepository(News::class)->find($deletePost->getId());
        try {
            $em->remove($news);
            $em->flush();
        } catch (Exception $e) {
            throw new Exception('Delete post failed', 0, $e);
        }
        $employee = $em->getRepository(Employee::class)->find($deletePost->getDeletingEmployeeId());
        $message = sprintf(
            "*%s %s* has deleted a dashboard post \"*%s*\"",
            $employee->getName(),
            $employee->getLastName(),
            $news->getTitle(),
        );
        $this->eventDispatcher->dispatch(new SlackAdminLogEvent($message));
    }
}
