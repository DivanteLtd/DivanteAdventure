<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.04.19
 * Time: 12:25
 */

namespace Divante\Bundle\AdventureBundle\Query\FreeDays;

interface FreeDaysReportQuery
{
    /** @return array<int,array<string,mixed>> */
    public function getReport() :array;
}
