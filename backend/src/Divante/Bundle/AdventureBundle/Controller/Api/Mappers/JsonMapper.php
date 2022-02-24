<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 13.08.18
 * Time: 13:54
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\Mappers;

class JsonMapper implements MapperInterface
{

    /**
     * @param string $data
     * @return mixed
     */
    public function map($data)
    {
        return json_decode($data, true);
    }
}
