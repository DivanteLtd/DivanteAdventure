<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Repository\ConfigEntryRepository;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Mappers\ConfigEntryMapper;
use Divante\Bundle\AdventureBundle\Message\UpdateConfig;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class ConfigurationController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/config")
 */
class ConfigurationController extends FOSRestController
{
    /**
     * @Route("")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param SystemConfig $config
     * @param ConfigEntryMapper $mapper
     * @return View
     * @throws \ReflectionException
     */
    public function getSystemEntries(SystemConfig $config, ConfigEntryMapper $mapper): View
    {
        $keys = $config->getKeys('KEY_', 4);
        /** @var ConfigEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository(ConfigEntry::class);
        $values = $repo->getConfigValues();
        $result = $this->getEntries($keys, $values, $mapper);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/content")
     * @Method("GET")
     * @param SystemConfig $config
     * @param ConfigEntryMapper $mapper
     * @return View
     * @throws \ReflectionException
     */
    public function getContentEntries(SystemConfig $config, ConfigEntryMapper $mapper): View
    {
        $keys = $config->getKeys('CONTENT_', 8);
        $values = $config->getContentValues();
        $result = $this->getEntries($keys, $values, $mapper);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @param array $keys
     * @param array $values
     * @param ConfigEntryMapper $mapper
     * @return array
     */
    public function getEntries(array $keys, array $values, ConfigEntryMapper $mapper) : array
    {
        $setKeys = array_map(fn(ConfigEntry $entry) => $entry->getKey(), $values);
        $result = array_map(fn(ConfigEntry $entry) => $mapper($entry), $values);
        foreach ($keys as $key) {
            if (!in_array($key, $setKeys)) {
                $entry = new ConfigEntry();
                $entry->setKey($key)
                    ->setValue('')
                    ->setCreatedAt();
                $result[] = $mapper($entry);
            }
        }
        return $result;
    }

    /**
     * @Route("/{key}")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param SystemConfig $config
     * @param ConfigEntryMapper $mapper
     * @param string $key
     * @return View
     */
    public function getHistory(SystemConfig $config, ConfigEntryMapper $mapper, string $key): View
    {
        $history = $config->getValueHistory($key);
        $result = array_map($mapper, $history);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function updateEntries(Request $request, MessageBusInterface $messageBus): View
    {
        /** @var array<string,string> $entries */
        $entries = $request->get('entries', []);
        /** @var User $user */
        $user = $this->getUser();
        $employeeId = $user->getEmployee()->getId();
        $message = new UpdateConfig($entries, $employeeId);
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }
}
