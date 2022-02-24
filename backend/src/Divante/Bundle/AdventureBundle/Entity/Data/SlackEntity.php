<?php

namespace Divante\Bundle\AdventureBundle\Entity\Data;

use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\ORM\Mapping as ORM;

trait SlackEntity
{
    /**
     * @var string|null
     * @ORM\Column(name="slack_id", type="text", nullable=true)
     */
    private $slackId;

    /**
     * @var int
     * @ORM\Column(name="slack_status", type="smallint")
     */
    private $slackStatus = SlackReceiver::SLACK_STATUS_NOT_ASKED;

    /**
     * @var string|null
     * @ORM\Column(name="slack_access_token", type="text", nullable=true)
     */
    private $slackAccessToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_slack_message_sent", type="datetime", nullable=true)
     */
    private $lastSlackMessageSent = null;

    public function getSlackId() : ?string
    {
        return $this->slackId;
    }

    public function setSlackId(?string $slackId) : SlackReceiver
    {
        $this->slackId = $slackId;
        if ($this instanceof SlackReceiver) {
            return $this;
        } else {
            throw new \Exception("SlackEntity should implement SlackReceiver interface");
        }
    }

    public function getSlackStatus() : int
    {
        return $this->slackStatus;
    }

    public function setSlackStatus(int $slackStatus) : SlackReceiver
    {
        $this->slackStatus = $slackStatus;
        if ($this instanceof SlackReceiver) {
            return $this;
        } else {
            throw new \Exception("SlackEntity should implement SlackReceiver interface");
        }
    }

    public function getSlackAccessToken() : ?string
    {
        return $this->slackAccessToken;
    }

    public function setSlackAccessToken(?string $accessToken) : SlackReceiver
    {
        $this->slackAccessToken = $accessToken;
        if ($this instanceof SlackReceiver) {
            return $this;
        } else {
            throw new \Exception("SlackEntity should implement SlackReceiver interface");
        }
    }

    public function getLastSlackMessageSent(): ?\DateTime
    {
        return $this->lastSlackMessageSent;
    }

    public function setLastSlackMessageSent(?\DateTime $lastSlackMessageSent): SlackReceiver
    {
        $this->lastSlackMessageSent = $lastSlackMessageSent;
        if ($this instanceof SlackReceiver) {
            return $this;
        } else {
            throw new \Exception("SlackEntity should implement SlackReceiver interface");
        }
    }
}
