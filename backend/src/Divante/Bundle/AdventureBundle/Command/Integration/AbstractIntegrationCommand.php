<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractIntegrationCommand extends ContainerAwareCommand
{
    private Client $client;

    final protected function configure() : void
    {
        $name = $this->getCommandName();
        $namespace = $this->getCommandNamespace();
        $this->setName("adventure:$namespace:$name")
            ->setDescription($this->getCommandDescription());
    }

    final public function run(InputInterface $input, OutputInterface $output) : int
    {
        $namespace = $this->getCommandNamespace();
        $configName = "adventure.${namespace}_integration";
        $config = $this->getContainer()->getParameter($configName);
        if (!is_array($config)) {
            $output->writeln("$configName parameter must be an object.");
        } elseif (!is_null($error = $this->validateConfiguration($config))) {
            $output->writeln($error);
        } else {
            $this->client = new Client();
            $this->callApi($output);
        }
        return 0;
    }

    /**
     * @param array<string,mixed> $config configuration object loaded from parameters.yml file
     * @return string|null string with error message if configuration is invalid, NULL if configuration is valid
     */
    protected function validateConfiguration(array $config) : ?string
    {
        if (!$config['enabled']) {
            return 'Integration is disabled';
        } else {
            return null;
        }
    }

    protected function getClient() : Client
    {
        return $this->client;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array<string,mixed> $urlVars
     * @param array<string,mixed>|null $jsonData
     * @return array<int,string|mixed>
     */
    protected function runRequest(
        string $url,
        string $method = 'GET',
        array $urlVars = [],
        ?array $jsonData = null
    ) : array {
        foreach ($urlVars as $key => $value) {
            $url = str_replace('{'.$key.'}', $value, $url);
        }
        $data = [
            'auth' => $this->getAuth(),
            'headers' => $this->getHeaders()
        ];
        if (!is_null($jsonData)) {
            $data['json'] = $jsonData;
        }
        $response = $this->getClient()->request($method, $url, $data);
        if ($response->getStatusCode() === Response::HTTP_OK) {
            $json = $this->streamInterfaceToString($response->getBody());
            return json_decode($json, true);
        }
        return [];
    }

    protected function streamInterfaceToString(StreamInterface $body) : string
    {
        $result = "";
        do {
            $part = $body->read(1024);
            $result .= $part;
        } while (strlen($part) > 0);
        return $result;
    }

    /** @return string[] */
    protected function getAuth() : array
    {
        return [];
    }

    /** @return array<string,string> */
    protected function getHeaders() : array
    {
        return [];
    }

    abstract protected function getCommandNamespace() : string;
    abstract protected function getCommandName() : string;
    abstract protected function getCommandDescription() : string;
    abstract protected function callApi(OutputInterface $output) : void;
}
