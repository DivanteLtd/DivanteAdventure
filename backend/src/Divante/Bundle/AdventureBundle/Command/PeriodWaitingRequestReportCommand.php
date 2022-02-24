<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 09.08.18
 * Time: 16:01
 */

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PeriodWaitingRequestReportCommand extends ContainerAwareCommand
{
    private EmailConfiguration $email;
    private EntityManagerInterface $em;
    protected TranslatorInterface $translator;

    public function __construct(EmailConfiguration $email, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->email = $email;
        $this->em = $em;
        $this->translator = $translator;
    }

    /** @inheritdoc */
    protected function configure() : void
    {
        $this
            ->setName('adventure:requests:awaiting:approval')
            ->setDescription('Send email with awaiting days off request list');
    }

    /** @inheritdoc */
    public function run(InputInterface $input, OutputInterface $output) : int
    {
        $this->translator->setLocale('en');
        $repoDays = $this->em->getRepository(LeaveRequest::class);
        $requests = $repoDays->findBy(['status' => LeaveRequest::REQUEST_STATUS_PENDING]);
        $this->email->sendMessage(
            $this->email->getMailerBokAddress(),
            null,
            sprintf(
                '[Adventure] #%d %s',
                count($requests),
                $this->translator->trans('requestArePending')
            ),
            ['employeeDaysoffRequest' => $requests],
            'AdventureBundle:Emails:daysoff_request_check_pending.html.twig'
        );
        return 0;
    }
}
