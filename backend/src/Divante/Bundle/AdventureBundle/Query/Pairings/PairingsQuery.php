<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 14:09
 */

namespace Divante\Bundle\AdventureBundle\Query\Pairings;

interface PairingsQuery
{
    /**
     * @param int $start
     * @param int $stop
     * @return PairingsView[]
     */
    public function getByTimestamps(int $start, int $stop) :array;

    /**
     * @param array<string,mixed> $data
     * @return PairingsView
     */
    public function getByData(array $data) : PairingsView;
}
