<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 11.04.19
 * Time: 12:48
 */

namespace  Divante\Bundle\AdventureBundle\Infrastructure\Config;

use Divante\Bundle\AdventureBundle\Query\Role\RoleQuery as RQ;

class RoleQuery implements RQ
{
    /** @var string[] */
    private array $rolesHierarchy;
    /** @var string[]|null */
    private ?array $roles = null;

    /**
     * RoleQuery constructor.
     * @param string[] $rolesHierarchy
     */
    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    /** @inheritDoc */
    public function getAll(): array
    {
        if ($this->roles) {
            return $this->roles;
        }

        $roles = [];
        array_walk_recursive($this->rolesHierarchy, function ($val) use (&$roles) {
            $roles[] = $val;
        });

        return $this->roles = array_unique($roles);
    }
}
