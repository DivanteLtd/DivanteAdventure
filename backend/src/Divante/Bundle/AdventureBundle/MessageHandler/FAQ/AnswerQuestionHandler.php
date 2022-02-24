<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Message\FAQ\AnswerQuestion;

class AnswerQuestionHandler extends AbstractAskedQuestionHandler
{
    public function __invoke(AnswerQuestion $message) : void
    {
        $this->handle($message, null);
    }
}
