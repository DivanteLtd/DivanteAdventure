<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 11.04.19
 * Time: 12:45
 */

namespace Divante\Bundle\AdventureBundle\Query\Role;

interface RoleQuery
{
    /** @return string[] */
    public function getAll() :array;
}
