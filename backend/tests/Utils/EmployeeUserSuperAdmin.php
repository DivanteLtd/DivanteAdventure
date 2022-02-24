<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */


namespace Tests\Utils;


use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;

class EmployeeUserSuperAdmin
{
    public static function getEmployee()
    {
    /** @var User $user */
    $user = new User();
    $user->setRoles([ User::ROLE_SUPER_ADMIN ]);
    $employee = new Employee();
    $reflection = new \ReflectionObject($employee);
    $userProperty = $reflection->getProperty('fosUser');
    $userProperty->setAccessible(true);
    $userProperty->setValue($employee, $user);
    return $employee;
    }
}