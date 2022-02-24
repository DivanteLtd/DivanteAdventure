<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

class SlackContextBlock implements SlackSendableMessage
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toJson(): array
    {
        return [
            "type" => "context",
            "elements" => [
                [
                    "type" => "mrkdwn",
                    "text" => $this->text,
                ]
            ]
        ];
    }
}
