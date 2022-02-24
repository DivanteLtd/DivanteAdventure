<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\ORM\EntityManagerInterface;

class SlackAdminReceiver implements SlackReceiver
{
    public const SLACK_TYPE = "AdminReceiver";

    private SystemConfig $systemConfig;
    private EntityManagerInterface $em;

    public function __construct(SystemConfig $systemConfig, EntityManagerInterface $em)
    {
        $this->systemConfig = $systemConfig;
        $this->em = $em;
    }

    public function getId(): int
    {
        return -1;
    }

    public function getName(): string
    {
        return 'admin notification feed';
    }

    public function getSlackType(): string
    {
        return self::SLACK_TYPE;
    }

    public function getSlackId(): ?string
    {
        return $this->getValue(SystemConfig::KEY_SLACK_ADMIN_ID);
    }

    public function setSlackId(?string $id): SlackReceiver
    {
        $this->updateValue(SystemConfig::KEY_SLACK_ADMIN_ID, $id);
        return $this;
    }

    public function getSlackAccessToken(): ?string
    {
        return $this->getValue(SystemConfig::KEY_SLACK_ADMIN_ACCESS_TOKEN);
    }

    public function setSlackAccessToken(?string $accessToken): SlackReceiver
    {
        $this->updateValue(SystemConfig::KEY_SLACK_ADMIN_ACCESS_TOKEN, $accessToken);
        return $this;
    }

    public function getSlackStatus(): int
    {
        $val = $this->getValue(SystemConfig::KEY_SLACK_ADMIN_STATUS);
        if (is_null($val)) {
            return SlackReceiver::SLACK_STATUS_NOT_ASKED;
        }
        return (int)$val;
    }

    public function setSlackStatus(int $slackStatus): SlackReceiver
    {
        $this->updateValue(SystemConfig::KEY_SLACK_ADMIN_STATUS, (string)$slackStatus);
        return $this;
    }

    public function getLastSlackMessageSent(): ?DateTime
    {
        return null;
    }

    public function setLastSlackMessageSent(?DateTime $lastSlackMessageSent): SlackReceiver
    {
        return $this;
    }

    public function getSlackLanguage(): string
    {
        return 'en';
    }

    private function getValue(string $key): ?string
    {
        $val = $this->systemConfig->getValueOrDefault($key, '');
        return empty($val) ? null : $val;
    }

    private function updateValue(string $key, ?string $value): void
    {
        $oldEntry = $this->systemConfig->getValue($key);
        if (!is_null($oldEntry)) {
            $oldEntry->setReplacedAt();
        }
        $configEntry = new ConfigEntry();
        $configEntry
            ->setCreatedAt()
            ->setKey($key)
            ->setGroup(explode(".", $key)[0] ?? '')
            ->setValue($value ?? '');
        $this->em->persist($configEntry);
        $this->em->flush();
    }
}
