<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 13:10
 */
namespace  Divante\Bundle\AdventureBundle\Query\EmployeeProject;

interface EmployeeProjectQuery
{
    /** @return EmployeeProjectView[] */
    public function getAll() :array;
    public function getById(int $id) :EmployeeProjectView;
}
