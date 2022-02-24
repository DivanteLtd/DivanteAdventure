<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Message\FAQ\RejectQuestion;

class RejectQuestionHandler extends AbstractAskedQuestionHandler
{
    public function __invoke(RejectQuestion $message) : void
    {
        $this->handle($message, $message->getRejectMessage());
    }
}
