<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\SendEmail;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SendEmailHandler
{
    private EntityManagerInterface $em;
    private EmailConfiguration $emailConfiguration;
    protected TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        EmailConfiguration $mailConfig,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->emailConfiguration = $mailConfig;
        $this->translator = $translator;
    }

    public function __invoke(SendEmail $message) : void
    {
        /** @var Project|null $project */
        $project = $this->em->getRepository(Project::class)->find($message->getProjectId());
        /** @var EmployeeProject[] $employeeProject */
        $employeeProject = $this->em->getRepository(EmployeeProject::class)->findBy(
            ['project' => $project]
        );

        foreach ($employeeProject as $entry) {
            $datesTo = $entry->getDateTo();
            $datesFrom = $entry->getDateFrom();
            $employee = $entry->getEmployee();
            if (count($datesTo) > count($datesFrom)) {
                $this->sendEmail($employee, $project);
            } else {
                foreach ($datesTo as $dateTo) {
                    $today = strtotime(date_format(new DateTime(), 'Y-m'));
                    $date = DateTime::createFromFormat('m-Y', $dateTo)->getTimestamp();
                    if ($date >= $today) {
                        $this->sendEmail($employee, $project);
                    }
                }
            }
        }
    }

    protected function sendEmail(Employee $employee, Project $project) : void
    {
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('informationAboutDataProcessingInTheProject');
        $this->emailConfiguration->sendMessage(
            $employee->getEmail(),
            null,
            sprintf('%s %s', $subject, $project->getName()),
            [
                'projectName' => $project->getName(),
                'criteria' => $project->getCriteria()->toArray(),
                'language' => $language
            ],
            'AdventureBundle:Emails:criteria_report.html.twig'
        );
    }

    /**
     * @param EmployeeProject[] $employeeProject
     * @return Employee[]
     */
    protected function getEmployees(array $employeeProject) : array
    {
        $employees = array_map(function (EmployeeProject $ep) {
            return $ep->getEmployee();
        }, $employeeProject);

        return $employees;
    }
}
