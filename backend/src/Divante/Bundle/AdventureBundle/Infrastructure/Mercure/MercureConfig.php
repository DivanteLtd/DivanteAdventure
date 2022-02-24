<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Mercure;

class MercureConfig
{
    private string $jwtKey;
    private string $mercureUrl;

    public function __construct(string $jwtKey, string $mercureUrl)
    {
        $this->jwtKey = $jwtKey;
        $this->mercureUrl = $mercureUrl;
    }

    public function getJwtKey(): string
    {
        return $this->jwtKey;
    }

    public function getMercureUrl(): string
    {
        return $this->mercureUrl;
    }
}
