<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 13.08.18
 * Time: 13:53
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\Mappers;

interface MapperInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function map($data);
}
