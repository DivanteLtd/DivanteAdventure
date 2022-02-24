<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

abstract class AbstractChecklistMapper implements Mapper
{
    /**
     * @param array<Employee> $employees
     * @return array<string,mixed>
     */
    protected function getEmployeeData(array $employees) : array
    {
        return array_map(fn(Employee $employee):array => [
            'id' => $employee->getId(),
            'name' => $employee->getName(),
            'lastName' => $employee->getLastName(),
            'photo' => $employee->getPhoto(),
        ], $employees);
    }

    /**
     * @param Checklist $checklist
     * @return array<string,mixed>
     */
    protected function getBaseData(Checklist $checklist) : array
    {
        $data = [
            'id' => $checklist->getId(),
            'type' => $checklist->getType(),
            'namePl' => $checklist->getNamePl(),
            'nameEn' => $checklist->getNameEn(),
            'subject' => $this->getEmployeeData([$checklist->getSubject()]),
            'startedAt' => $checklist->getStartedAt()->getTimestamp(),
            'hidden' => $checklist->isHidden(),
            'dueDate' => $checklist->getDueDate()->getTimestamp()
        ];
        $finishedAt = $checklist->getFinishedAt();
        if (!is_null($finishedAt)) {
            $data['finishedAt'] = $finishedAt->getTimestamp();
        }

        $owners = $checklist->getOwners()->toArray();
        if (!empty($owners)) {
            $data['owners'] = $this->getEmployeeData($owners);
        }

        return $data;
    }

    /**
     * @param Checklist $checklist
     * @return array<string,mixed>
     */
    public function __invoke(Checklist $checklist) : array
    {
        return $this->mapEntity($checklist);
    }
}
