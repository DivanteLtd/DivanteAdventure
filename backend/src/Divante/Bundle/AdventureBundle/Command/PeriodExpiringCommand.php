<?php
/**
 * Check existing employee periods and send email with alert for expiring ones.
 */

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\Repository\LeavePeriodRepository;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PeriodExpiringCommand extends ContainerAwareCommand
{
    private EntityManagerInterface $em;
    private EmailConfiguration $email;
    protected TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $em, EmailConfiguration $email, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->em = $em;
        $this->email = $email;
        $this->translator = $translator;
    }

    /** @inheritdoc */
    protected function configure() : void
    {
        $this
            ->setName('adventure:employeeperiod:expiring')
            ->setDescription('Get expiring in given month or non existing employee periods')
            ->addOption(
                'expiring',
                null,
                InputOption::VALUE_OPTIONAL,
                'Periods expiring in given month number',
                1
            );
    }

    /** @inheritdoc */
    public function run(InputInterface $input, OutputInterface $output) : int
    {
        /** @var LeavePeriodRepository $repo */
        $repo = $this->em->getRepository(LeavePeriod::class);
        $expiring = (int)$input->getOption('expiring');
        $periods = $repo->expiringPeriodReport($expiring);
        if (!empty($periods)) {
            $email = $this->email;
            $this->translator->setLocale('pl');
            $email->sendMessage(
                $email->getMailerBokAddress(),
                null,
                sprintf('[Adventure] %s', $this->translator->trans('expiringDaysOffPeriods')),
                [
                    'periods' => $periods,
                ],
                'AdventureBundle:Emails:daysoff_period_expiring.html.twig'
            );
        }
        return 0;
    }
}
