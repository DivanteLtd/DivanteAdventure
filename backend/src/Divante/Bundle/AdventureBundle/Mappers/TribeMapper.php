<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Closure;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;

class TribeMapper implements Mapper
{
    /**
     * @param Tribe $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $positionMapper = new PositionMapper();

        $responsible = [];
        $tribeResponsible = $entity->getResponsible();
        /** @var Employee $employee */
        foreach ($tribeResponsible as $employee) {
            $responsible[] = [
                'id' => $employee->getId(),
                'name' => $employee->getName(),
                'lastName' => $employee->getLastName(),
                'photo' => $employee->getPhoto(),
            ];
        }
        $techLeader = null;
        if ($entity->getTechLeader() !== null) {
            $techLeader = [
                'id' => $entity->getTechLeader()->getId(),
                'name' => $entity->getTechLeader()->getName(),
                'lastName' => $entity->getTechLeader()->getLastName(),
                'photo' => $entity->getTechLeader()->getPhoto(),
            ];
        }
        return [
            'id' => $entity->getId(),
            'photoUrl' => $entity->getPhoto(),
            'name' => $entity->getName(),
            'url' => $entity->getUrl(),
            'isVirtual' => $entity->isVirtual(),
            'visibility' => $entity->getVisibility(),
            'description' => $entity->getDescription(),
            'employeesCount' => $entity->getEmployees()->filter(fn(Employee $e) : bool => !$e->isFormer())->count(),
            'positions' => $entity->getPositions()->map(Closure::fromCallable($positionMapper))->toArray(),
            'connectedToSlack' => $entity->getSlackStatus() === Tribe::SLACK_AUTHORIZED,
            'responsible' => $responsible,
            'hrEmail' => $entity->getHrEmail(),
            'freeDayCategoryId' => $entity->getFreeDayCategoryId(),
            'freeDayProjectId' => $entity->getFreeDayProjectId(),
            'sickLeaveCategoryId' => $entity->getSickLeaveCategoryId(),
            'sickLeaveProjectId' => $entity->getSickLeaveProjectId(),
            'techLeader' => $techLeader,
        ];
    }

    /**
     * @param Tribe $entity
     * @return array<string,mixed>
     */
    public function __invoke(Tribe $entity) : array
    {
        return $this->mapEntity($entity);
    }
}
