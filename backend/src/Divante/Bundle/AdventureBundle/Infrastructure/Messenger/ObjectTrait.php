<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Messenger;

trait ObjectTrait
{

    public function __toString()
    {
        $props = get_object_vars($this);
        return json_encode($props, JSON_UNESCAPED_UNICODE);
    }
}
