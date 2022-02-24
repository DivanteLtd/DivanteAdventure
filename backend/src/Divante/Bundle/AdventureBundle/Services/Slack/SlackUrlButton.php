<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

class SlackUrlButton implements SlackSendableMessage
{
    /** @var string */
    private $url;
    /** @var string */
    private $label;
    /** @var string|null */
    private $style;

    public const BUTTON_STYLE_DEFAULT = null;
    public const BUTTON_STYLE_PRIMARY = 'primary';
    public const BUTTON_STYLE_DANGER = 'danger';

    public function __construct(string $url, string $label, ?string $style = self::BUTTON_STYLE_DEFAULT)
    {
        $this->url = $url;
        $this->label = $label;
        $this->style = $style;
    }

    public function toJson(): array
    {
        $json = [
            "type" => "button",
            "text" => [
                "type" => "plain_text",
                "text" => $this->label,
                "emoji" => true,
            ],
            "url" => $this->url,
        ];
        if (!is_null($this->style)) {
            $json['style'] = $this->style;
        }
        return $json;
    }
}
