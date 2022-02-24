<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

interface ISOAcceptationQuery
{
    /** @return ISOAcceptationView[] */
    public function get() : array;
}
