<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

interface GDPRAcceptationQuery
{
    /** @return GDPRAcceptationView[] */
    public function get() :array;
}
