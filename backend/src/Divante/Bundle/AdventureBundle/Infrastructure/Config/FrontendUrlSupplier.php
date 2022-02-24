<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Config;

class FrontendUrlSupplier
{
    /** @var string */
    private $frontendUrl;

    public function __construct(string $frontendUrl)
    {
        $this->frontendUrl = $frontendUrl;
    }

    public function getFrontendUrl() : string
    {
        return $this->frontendUrl;
    }
}
