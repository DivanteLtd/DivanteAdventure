<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser;

/**
 * Class Parser converts input from advanced filters to SQL "where" partial. For example, if user passes input:
 *
 *     firstName="Norbert" or tribeName = "A-Team"
 *
 * And programmer will define that "firstName" field corresponds to "e.firstname" column and "tribeName" field
 * corresponds to "t.name" column, then parser should return:
 *
 *      e.firstname = "Norbert" OR t.name = "A-Team"
 *
 * For future:
 * - parentheses
 * - "IN"
 * - NOT
 * - LIKE
 * @package Divante\Bundle\AdventureBundle\Services\FilterParser
 */
interface ParserInterface
{
    /**
     * @param string $filterInput
     * @param Field[] $fields
     * @return string
     */
    public function parse(string $filterInput, array $fields) : string;
}
