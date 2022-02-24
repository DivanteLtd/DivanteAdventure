<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

class SlackSectionBlock implements SlackSendableMessage
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toJson(): array
    {
        return [
            "type" => "section",
            "text" => [
                "type" => "mrkdwn",
                "text" => $this->text,
            ]
        ];
    }
}
