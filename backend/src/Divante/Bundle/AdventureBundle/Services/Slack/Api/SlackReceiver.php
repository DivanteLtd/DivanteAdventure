<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\Api;

use Divante\Bundle\AdventureBundle\Entity\Data\NamedEntity;

interface SlackReceiver extends NamedEntity
{
    public const SLACK_STATUS_NOT_ASKED = 0;
    public const SLACK_STATUS_ASKED = 1;
    public const SLACK_WAITING_FOR_AUTHORIZATION = 2;
    public const SLACK_AUTHORIZED = 3;

    public function getSlackType() : string;
    public function getSlackId() : ?string;
    public function setSlackId(?string $id) : SlackReceiver;
    public function getSlackAccessToken() : ?string;
    public function setSlackAccessToken(?string $accessToken) : SlackReceiver;
    public function getSlackStatus() : int;
    public function setSlackStatus(int $slackStatus) : SlackReceiver;
    public function getLastSlackMessageSent(): ?\DateTime;
    public function setLastSlackMessageSent(?\DateTime $lastSlackMessageSent): SlackReceiver;
    public function getSlackLanguage() : string;
}
