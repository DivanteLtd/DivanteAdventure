<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 20.05.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeleteNotification;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteNotificationHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteNotification $message
     * @throws Exception
     */
    public function __invoke(DeleteNotification $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        $entry = $em->getRepository(Notification::class)->find($id);
        if (is_null($entry)) {
            throw new Exception("Notification entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
