<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 19.20.19
 * Time: 09:41
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineAwareMapper
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    protected function getObjectManager() : EntityManagerInterface
    {
        return $this->em;
    }
}
