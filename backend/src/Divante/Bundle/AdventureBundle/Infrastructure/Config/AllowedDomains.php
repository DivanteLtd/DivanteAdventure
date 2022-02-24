<?php


namespace Divante\Bundle\AdventureBundle\Infrastructure\Config;

class AllowedDomains
{
    private array $domains;

    public function __construct(array $domains)
    {
        $this->domains = $domains;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }
}
