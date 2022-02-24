<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 20.12.18
 * Time: 12:59
 */
namespace Divante\Bundle\AdventureBundle\Mappers;

interface Mapper
{
    /**
     * @param mixed $entity
     * @return array<int|string,mixed>
     */
    public function mapEntity($entity) : array;
}
