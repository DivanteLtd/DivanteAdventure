<?php
namespace Divante\Bundle\AdventureBundle\Query\Report;

interface ReportQuery
{
    /**
     * @param array<string,mixed> $criteria
     * @return array<int,array<string,mixed>>
     */
    public function getByCriteria(array $criteria): array;
}
