<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreatePost;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreatePostHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param CreatePost $createPost
     * @throws Exception
     */
    public function __invoke(CreatePost $createPost) : void
    {
        $em = $this->em;
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)
            ->find($createPost->getEmployeeId());
        if (is_null($employee)) {
            throw new Exception("Author employee not found");
        }
        if (is_null($createPost->getTitle()) && is_null($createPost->getBanner())) {
            throw new Exception("Title and banner are not required, but at least one of them must be supplied.");
        }
        $news = new News();
        $news->setTitle($createPost->getTitle())
            ->setBanner($createPost->getBanner())
            ->setDescription($createPost->getDescription())
            ->setType($createPost->getType())
            ->setAuthor($employee)
            ->setUpdatedAt()
            ->setCreatedAt();
        try {
            $em->persist($news);
            $em->flush();
        } catch (Exception $e) {
            throw new Exception('Create post failed', 0, $e);
        }
        $message = sprintf(
            "*%s %s* has created a new dashboard post \"*%s*\"",
            $employee->getName(),
            $employee->getLastName(),
            $news->getTitle(),
        );
        $this->eventDispatcher->dispatch(new SlackAdminLogEvent($message));
    }
}
