<?php

namespace Divante\Bundle\AdventureBundle\Command\Feedback;

use DateInterval;
use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\PlannedFeedbackMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeedbackReminderCommand extends Command
{
    private EntityManagerInterface $em;
    private PlannedFeedbackMessage $slackMessage;

    public function __construct(EntityManagerInterface $em, PlannedFeedbackMessage $slackMessage)
    {
        $this->em = $em;
        $this->slackMessage = $slackMessage;
        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:feedback:reminder')
            ->setDescription('Sends reminds about incoming feedbacks.')
            ->addArgument('days', InputArgument::REQUIRED, 'number of days before planned feedback');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = (int)$input->getArgument('days');
        $date = (new DateTime())->add(DateInterval::createFromDateString($days.' days'));
        /** @var PlannedFeedback[] $entries */
        $entries = $this->em->getRepository(PlannedFeedback::class)->findBy([
            'date' => $date,
        ]);
        foreach ($entries as $entry) {
            $message = sprintf(
                "Sending notification for feedback #%s (leader: %s %s, employee: %s %s)...",
                $entry->getId(),
                $entry->getLeader()->getName(),
                $entry->getLeader()->getLastName(),
                $entry->getEmployee()->getName(),
                $entry->getEmployee()->getLastName(),
            );
            $output->write($message);
            $this->slackMessage->sendMessage($days, $entry);
            $output->writeln(" done.");
        }
        return 0;
    }
}
