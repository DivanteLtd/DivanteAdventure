<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Config;

class EnvironmentSettings
{
    private bool $demoEnabled;

    public function __construct()
    {
        $this->demoEnabled = (bool)($_ENV['DEMO_ENABLED'] ?? false);
    }

    public function isDemoEnabled(): bool
    {
        return $this->demoEnabled;
    }
}
