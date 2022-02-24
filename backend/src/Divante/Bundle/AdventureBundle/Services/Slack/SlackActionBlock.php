<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

class SlackActionBlock implements SlackSendableMessage
{
    /** @var SlackUrlButton[] */
    private array $buttons;

    /**
     * SlackActionBlock constructor.
     * @param SlackUrlButton[] $buttons
     */
    public function __construct(array $buttons)
    {
        $this->buttons = $buttons;
    }

    public function toJson(): array
    {
        $elements = [];
        foreach ($this->buttons as $button) {
            if ($button instanceof SlackUrlButton) {
                $elements[] = $button->toJson();
            }
        }
        return [
            "type" => "actions",
            "elements" => $elements,
        ];
    }
}
