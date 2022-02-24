<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\Api;

use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use GuzzleHttp\Client;

class SlackSender
{
    public const POST_API = "https://slack.com/api/chat.postMessage";

    public function send(SlackSendableMessage $message, SlackReceiver $receiver) : void
    {
        if (is_null($receiver->getSlackAccessToken()) || is_null($receiver->getSlackId())) {
            return;
        }
        if ($receiver->getSlackStatus() !== SlackReceiver::SLACK_AUTHORIZED) {
            return;
        }

        $json = $message->toJson();
        $json['channel'] = $receiver->getSlackId();
        $json['username'] = "Adventurebot";
        $json['as_user'] = "false";

        $headers = [
            "Authorization" => "Bearer ".$receiver->getSlackAccessToken(),
        ];

        $client = new Client();
        $client->request("POST", self::POST_API, [ "json" => $json, "headers" => $headers ]);
        $receiver->setLastSlackMessageSent(new \DateTime());
    }
}
