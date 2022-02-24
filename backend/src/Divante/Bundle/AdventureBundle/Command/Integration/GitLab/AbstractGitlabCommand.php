<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Command\Integration\AbstractIntegrationCommand;

abstract class AbstractGitlabCommand extends AbstractIntegrationCommand
{
    private string $token;
    private string $instanceUrl;

    /**
     * @param array<string,mixed> $config
     * @return string|null
     */
    protected function validateConfiguration(array $config): ?string
    {
        if (!is_null($error = parent::validateConfiguration($config))) {
            return $error;
        } elseif (!$config['token']) {
            return "Parameter 'token' is required";
        } elseif (!$config['instance_url']) {
            return "Parameter 'instance_url' is required";
        } else {
            $this->token = $config['token'];
            $this->instanceUrl = $config['instance_url'];
            return null;
        }
    }

    protected function getToken() : string
    {
        return $this->token;
    }

    protected function getInstanceUrl() : string
    {
        return $this->instanceUrl;
    }

    final protected function getCommandNamespace(): string
    {
        return 'gitlab';
    }

    final protected function getHeaders(): array
    {
        return [
            'Private-Token' => $this->getToken()
        ];
    }
}
