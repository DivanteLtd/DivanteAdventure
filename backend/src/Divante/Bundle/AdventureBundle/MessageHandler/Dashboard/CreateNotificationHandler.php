<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.05.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Infrastructure\Mercure\MercureUpdate;
use Divante\Bundle\AdventureBundle\Mappers\NotificationMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Doctrine\ORM\EntityManagerInterface;

class CreateNotificationHandler
{
    private EntityManagerInterface $em;
    private NotificationMapper $mapper;
    private MercureUpdate $update;

    public function __construct(EntityManagerInterface $em, MercureUpdate $update, NotificationMapper $mapper)
    {
        $this->em = $em;
        $this->mapper = $mapper;
        $this->update = $update;
    }

    /**
     * @param CreateNotification $message
     * @throws \Exception
     */
    public function __invoke(CreateNotification $message) : void
    {
        $em = $this->em;
        $em->getConnection()->beginTransaction();
        try {
            $notification = new Notification();
            $notification
                ->setEmployeeId($message->getEmployeeId())
                ->setDescription($message->getDescription())
                ->setSubject($message->getSubject())
                ->setPath($message->getPath())
                ->setBold(true);
            $em->persist($notification);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }

        $this->update->sendUpdate(
            $this->mapper->mapEntity($notification),
            sprintf("/notifications/%s", $message->getEmployeeId()),
            'new-notification'
        );
    }
}
