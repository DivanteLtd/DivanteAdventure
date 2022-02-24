<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

/**
 * Interface Structure
 * @package Divante\Bundle\AdventureBundle\Services\FilterParser\Structures
 */
interface Structure
{
    public function toSql() : string;
}
