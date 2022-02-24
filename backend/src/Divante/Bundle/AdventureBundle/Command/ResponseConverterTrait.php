<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Psr\Http\Message\StreamInterface;

trait ResponseConverterTrait
{
    private function streamInterfaceToString(StreamInterface $body) : string
    {
        $result = "";
        do {
            $part = $body->read(1024);
            $result .= $part;
        } while (strlen($part) > 0);
        return $result;
    }
}
