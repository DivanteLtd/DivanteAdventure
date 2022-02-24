<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Hardware;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteHardwareAgreementEntry
{
    use ObjectTrait;
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }
}
