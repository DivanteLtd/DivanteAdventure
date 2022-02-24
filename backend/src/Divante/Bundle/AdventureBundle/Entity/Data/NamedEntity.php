<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.12.18
 * Time: 11:39
 */

namespace Divante\Bundle\AdventureBundle\Entity\Data;

interface NamedEntity
{
    public function getId() : int;
    public function getName() : string;
}
