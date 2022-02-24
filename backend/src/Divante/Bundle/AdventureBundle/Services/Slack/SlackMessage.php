<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

class SlackMessage implements SlackSendableMessage
{
    private string $notification;
    /** @var SlackSendableMessage[] */
    private array $blocks;

    /**
     * SlackMessage constructor.
     * @param string $notification
     * @param SlackSendableMessage[] $blocks
     */
    public function __construct(string $notification, array $blocks = [])
    {
        $this->notification = $notification;
        $this->blocks = $blocks;
    }

    public function toJson(): array
    {
        $json = [
            'text' => $this->notification,
            'blocks' => [],
        ];

        foreach ($this->blocks as $block) {
            if ($block instanceof SlackSendableMessage) {
                $json['blocks'][] = $block->toJson();
            }
        }
        return $json;
    }
}
