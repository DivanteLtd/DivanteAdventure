<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Message\Dashboard\UpdateNotification;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateNotificationHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateNotification $message
     * @throws Exception
     */
    public function __invoke(UpdateNotification $message) : void
    {
        $em = $this->em;
        /** @var Notification|null $notification */
        $notification = $em->getRepository(Notification::class)->find($message->getEntryId());
        if (is_null($notification)) {
            $id = $message->getEntryId();
            throw new Exception(
                "Potential $notification with ID $id has not been found",
                Response::HTTP_NOT_FOUND
            );
        }
        $em->getConnection()->beginTransaction();
        try {
            $notification
                ->setBold(false);
            $em->persist($notification);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw new Exception("Updating notification failed", 0, $e);
        }
    }
}
