<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

interface MarketingAcceptationQuery
{
    /** @return MarketingAcceptationView[] */
    public function get() :array;
}
