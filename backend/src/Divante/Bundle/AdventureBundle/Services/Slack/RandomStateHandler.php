<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\ORM\EntityManagerInterface;

class RandomStateHandler
{
    public const TIME_FOR_AUTHORIZATION = 60 * 15; // 15 minutes

    private EntityManagerInterface $em;
    private SlackAdminReceiver $adminReceiver;

    public function __construct(EntityManagerInterface $entityManager, SlackAdminReceiver $adminReceiver)
    {
        $this->em = $entityManager;
        $this->adminReceiver = $adminReceiver;
    }

    /**
     * Clears receiver's Slack URL (if present) and generates a random state for authorization.
     * @param SlackReceiver $receiver
     * @return string
     */
    public function generateRandomState(SlackReceiver $receiver) : string
    {
        $payload = [
            'endTime' => time() + self::TIME_FOR_AUTHORIZATION,
            'identifier' => $receiver->getId(),
            'type' => $receiver->getSlackType(),
            'randomString' => base64_encode("-random-".rand(10000, 99999)),
        ];
        $payloadJson = json_encode($payload);
        $state = base64_encode($payloadJson);
        $receiver
            ->setSlackId($state)
            ->setSlackStatus(Employee::SLACK_WAITING_FOR_AUTHORIZATION);
        return $state;
    }

    public function getReceiverByState(string $state) : ?SlackReceiver
    {
        /** @var string|false $payloadJson */
        $payloadJson = base64_decode($state);
        if ($payloadJson === false) {
            return null;
        }
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }
        $endTime = (int)$payload['endTime'] ?? 0;
        if ($endTime < time()) {
            return null;
        }

        if ($payload['type'] === SlackAdminReceiver::SLACK_TYPE) {
            return $this->adminReceiver;
        }

        $identifier = (int)$payload['identifier'] ?? -1;
        $repo = $this->em->getRepository($payload['type']);
        $searchData = [
            'id' => $identifier,
            'slackId' => $state,
            'slackStatus' => SlackReceiver::SLACK_WAITING_FOR_AUTHORIZATION
        ];
        /** @var SlackReceiver|null $result */
        $result = $repo->findOneBy($searchData);
        return $result;
    }
}
