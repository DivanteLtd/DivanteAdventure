<?php

namespace Divante\Bundle\AdventureBundle\Message\Feedback;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

abstract class AbstractFeedbackMessage
{
    use ObjectTrait;

    private ?string $feedback;
    private ?string $progressFeedback;
    private ?string $technicalFeedback;

    public function __construct(?string $feedback, ?string $progressFeedback, ?string $technicalFeedback)
    {
        $this->feedback = $feedback;
        $this->progressFeedback = $progressFeedback;
        $this->technicalFeedback = $technicalFeedback;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function getProgressFeedback(): ?string
    {
        return $this->progressFeedback;
    }

    public function getTechnicalFeedback(): ?string
    {
        return $this->technicalFeedback;
    }
}
