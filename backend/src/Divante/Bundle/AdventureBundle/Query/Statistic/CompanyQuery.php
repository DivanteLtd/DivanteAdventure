<?php

namespace Divante\Bundle\AdventureBundle\Query\Statistic;

interface CompanyQuery
{
    public function getStatsByYearAndTribes(int $year, array $tribes) :array;
    public function getEmployeesByDateAndTribes(\DateTime $time, array $tribes) :array;
    public function getYears() :array;
    public function getTribes() :array;
}
