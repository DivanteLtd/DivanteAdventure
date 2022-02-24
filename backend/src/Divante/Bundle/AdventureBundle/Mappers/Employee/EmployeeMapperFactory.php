<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\User;

class EmployeeMapperFactory
{
    private UserEmployeeMapper $userEmployeeMapper;
    private AdministratorEmployeeMapper $administratorEmployeeMapper;
    private TribeMasterEmployeeMapper $tribeMasterEmployeeMapper;
    private ManagerHREmployeeMapper $managerHREmployeeMapper;
    private ManagerEmployeeMapper $managerEmployeeMapper;
    private HrEmployeeMapper $hrEmployeeMapper;

    public function __construct(
        UserEmployeeMapper $userEmployeeMapper,
        AdministratorEmployeeMapper $administratorEmployeeMapper,
        TribeMasterEmployeeMapper $tribeMasterEmployeeMapper,
        ManagerHREmployeeMapper $managerHREmployeeMapper,
        ManagerEmployeeMapper $managerEmployeeMapper,
        HrEmployeeMapper $hrEmployeeMapper
    ) {
        $this->userEmployeeMapper = $userEmployeeMapper;
        $this->administratorEmployeeMapper = $administratorEmployeeMapper;
        $this->tribeMasterEmployeeMapper = $tribeMasterEmployeeMapper;
        $this->managerHREmployeeMapper = $managerHREmployeeMapper;
        $this->managerEmployeeMapper = $managerEmployeeMapper;
        $this->hrEmployeeMapper = $hrEmployeeMapper;
    }

    public function getForSelf() : AbstractEmployeeMapper
    {
        return $this->administratorEmployeeMapper;
    }

    public function getFor(User $user) : AbstractEmployeeMapper
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            return $this->administratorEmployeeMapper;
        } elseif (in_array('ROLE_TRIBE_MASTER', $roles)) {
            return $this->tribeMasterEmployeeMapper;
        } elseif (in_array('ROLE_MANAGER', $roles) && in_array('ROLE_HR', $roles)) {
            return $this->managerHREmployeeMapper;
        } elseif (in_array('ROLE_MANAGER', $roles)) {
            return $this->managerEmployeeMapper;
        } elseif (in_array('ROLE_HR', $roles)) {
            return $this->hrEmployeeMapper;
        } else {
            return $this->userEmployeeMapper;
        }
    }
}
