<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SendWelcomeEmailCommand extends Command
{
    private EntityManagerInterface $em;
    private EmailSenderInterface $mailer;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        EmailSenderInterface $mailer,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->translator = $translator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('adventure:email:send-welcome')
            ->setDescription('Send welcome e-mails to new people');
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee[] $employees */
        $employees = $repo->findBy([
            'designatedHireDate' => new \DateTime(),
            'status' => PotentialEmployee::STATUS_ACCEPTED,
        ]);
        foreach ($employees as $employee) {
            $this->translator->setLocale($employee->getLanguage());
            $this->mailer->sendMessage(
                $employee->getEmail(),
                null,
                $this->translator->trans('mail.welcome.subject'),
                [
                    'employee' => $employee,
                ],
                'AdventureBundle:Emails:potential/accepted_potential_new_person.html.twig'
            );
        }
        return 0;
    }
}
