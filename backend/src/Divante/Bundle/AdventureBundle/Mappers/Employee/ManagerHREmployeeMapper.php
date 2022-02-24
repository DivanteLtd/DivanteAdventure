<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class ManagerHREmployeeMapper extends AbstractEmployeeMapper
{
    private UserEmployeeMapper $userEmployeeMapper;
    private HrEmployeeMapper $hrMapper;

    public function __construct(UserEmployeeMapper $userEmployeeMapper, HrEmployeeMapper $hrMapper)
    {
        $this->userEmployeeMapper = $userEmployeeMapper;
        $this->hrMapper = $hrMapper;
    }

    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return array_merge(
            $this->userEmployeeMapper->mapEmployeeToJson($entity),
            $this->hrMapper->mapEmployeeToJson($entity),
            [
                'worktime' => $entity->getJobTimeValue(),
                'jobTimeValue' => $entity->getJobTimeValue(),
                'childCare' => $entity->getChildCare(),
                "roles" => $this->getRoles($entity),
            ]
        );
    }

    /**
     * @param Employee $employee
     * @return string[]
     */
    private function getRoles(Employee $employee) : array
    {
        $user = $employee->getUser();
        $roles = [ 'ROLE_USER' ];
        if (!is_null($user)) {
            $roles = $user->getRoles();
        }
        return $roles;
    }
}
