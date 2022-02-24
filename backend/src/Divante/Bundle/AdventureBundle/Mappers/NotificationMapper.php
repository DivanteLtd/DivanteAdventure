<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Notification;

class NotificationMapper implements Mapper
{
    /**
     * @param Notification $notification
     * @return array<string,mixed>
     */
    public function __invoke(Notification $notification): array
    {
        return [
            'id' => $notification->getId(),
            'employeeId' => $notification->getEmployeeId(),
            'description' => $notification->getDescription(),
            'subject' => $notification->getSubject(),
            'path' => $notification->getPath(),
            'bold' => $notification->getBold(),
        ];
    }

    /**
     * @param Notification $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return $this($entity);
    }
}
