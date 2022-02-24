<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Link;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateLink;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateLinkHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateLink $message
     * @throws Exception
     */
    public function __invoke(CreateLink $message) : void
    {
        $title = $message->getTitle();
        $url = $message->getUrl();
        $userId = $message->getUserId();
        $em = $this->em;
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($userId);
        if (is_null($employee)) {
            throw new Exception("Employee with ID $userId does not exist.");
        }
        $em->beginTransaction();
        try {
            $link = (new Link())
                ->setTitle($title)
                ->setUrl($url)
                ->setAuthor($employee)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($link);
            $em->flush();
            $em->commit();
        } catch (Exception $e) {
            $em->rollback();
            throw new Exception($e);
        }
    }
}
