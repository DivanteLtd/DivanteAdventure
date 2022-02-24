<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Link;
use Divante\Bundle\AdventureBundle\Message\Dashboard\UpdateLink;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateLinkHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateLink $message
     * @throws Exception
     */
    public function __invoke(UpdateLink $message) : void
    {
        $id = $message->getId();
        $title = $message->getTitle();
        $url = $message->getUrl();
        $userId = $message->getAuthorId();
        $em = $this->em;
        $link = $em->getRepository(Link::class)->find($id);
        $employee = $em->getRepository(Employee::class)->find($userId);
        $em->beginTransaction();
        try {
            $link->setTitle($title)
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
