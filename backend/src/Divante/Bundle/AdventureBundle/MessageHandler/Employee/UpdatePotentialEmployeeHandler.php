<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdatePotentialEmployee;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class UpdatePotentialEmployeeHandler extends AbstractPotentialEmployeeHandler
{
    private string $helpdeskEmail;
    private string $personnelEmail;
    private string $receptionEmail;
    private EmailSenderInterface $mailer;
    private TranslatorInterface $translator;

    public function __construct(
        EmailConfig $emailConfig,
        EmailSenderInterface $mailer,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->helpdeskEmail = $emailConfig->getHelpdeskDepartmentEmail();
        $this->personnelEmail = $emailConfig->getAdmPersonnelEmail();
        $this->receptionEmail = $emailConfig->getReceptionEmail();
        $this->mailer = $mailer;
        $this->translator = $translator;
        parent::__construct($em);
    }

    /**
     * @param UpdatePotentialEmployee $message
     * @throws \Exception
     */
    public function __invoke(UpdatePotentialEmployee $message) : void
    {
        $employee = $this->getEmployee($message->getId());
        $recruiterId = $message->getRecruiterId();
        if ($recruiterId) {
            $recruiter = $this->entityManager->getRepository(Employee::class)->find($recruiterId);
        }
        $welcomeDay = is_null($message->getWelcomeDay()) ? null : new \DateTimeImmutable($message->getWelcomeDay());
        $employee
            ->setName($message->getName() ?? $employee->getName())
            ->setLastName($message->getLastName() ?? $employee->getLastName())
            ->setStatus($message->getStatus() ?? $employee->getStatus())
            ->setEmail($message->getEmail() ?? $employee->getEmail())
            ->setDesignatedPosition($this->getPosition($message) ?? $employee->getDesignatedPosition())
            ->setDesignatedTribe($this->getTribe($message) ?? $employee->getDesignatedTribe())
            ->setDesignatedHireDate($this->getHireDate($message))
            ->setJoinedEmployee($this->getJoinedEmployee($message))
            ->setRejectionCause($message->getRejectionCause())
            ->setContractType($message->getContractType() ?? $employee->getContractType())
            ->setGender($message->getGender() ?? $employee->getGender())
            ->setDateOfBirth($this->getDateOfBirthDate($message) ?? $employee->getDateOfBirth())
            ->setPrivatePhone($message->getPrivatePhone() ?? $employee->getPrivatePhone())
            ->setCity($message->getCity() ?? $employee->getCity())
            ->setPostalCode($message->getPostalCode() ?? $employee->getPostalCode())
            ->setStreet($message->getStreet() ?? $employee->getStreet())
            ->setCountry($message->getCountry() ?? $employee->getCountry())
            ->setPrivateEmail($message->getPrivateEmail() ?? $employee->getPrivateEmail())
            ->setRecruiter($recruiter ?? $employee->getRecruiter())
            ->setSource($message->getSource() ?? $employee->getSource())
            ->setCompany($message->getCompany() ?? $employee->getCompany())
            ->setWelcomeDayDate($welcomeDay ?? $employee->getWelcomeDayDate())
            ->setLanguage($message->getLanguage() ?? $employee->getLanguage())
            ->setNip($message->getNip())
            ->setFirmName($message->getFirmName())
            ->setFirmAddress($message->getFirmAddress())
            ->setUpdatedAt();
        try {
            $this->entityManager->flush();
            if (!is_null($message->getStatus())) {
                if ($message->getStatus() === PotentialEmployee::STATUS_ACCEPTED) {
                    $this->translator->setLocale('en');
                    $person= [
                        'name' => $employee->getName(),
                        'lastName' => $employee->getLastName(),
                        'email' => $employee->getEmail(),
                        'position' => $employee->getDesignatedPosition()
                            ? $employee->getDesignatedPosition()->getName() : null,
                        'tribe' =>  $employee->getDesignatedTribe()->getName(),
                        'hireDate' => $employee->getDesignatedHireDate()->format('Y-m-d'),
                        'welcomeDay' => $employee->getWelcomeDayDate()->format('Y-m-d'),
                        'avazaTrainingDate' => $employee->getWelcomeDayDate()->modify('+2 day')->format('Y-m-d'),
                        'city' => $employee->getCity(),
                        'postalCode' => $employee->getPostalCode(),
                        'street' => $employee->getStreet(),
                        'privatePhone' => $employee->getPrivatePhone(),
                        'privateEmail' => $employee->getPrivateEmail(),
                        'recruiterName' => $employee->getRecruiter()->getName(),
                        'recruiterLastName' => $employee->getRecruiter()->getLastName()
                    ];
                    $subject = sprintf(
                        '[Adventure] %s %s - %s %s %s.',
                        $this->translator->trans('startOfCooperation'),
                        $person['name'],
                        $person['lastName'],
                        $person['position'],
                        $person['hireDate'],
                    );
                    $this->sendEmail(
                        $person,
                        $subject,
                        $this->helpdeskEmail,
                        $employee->getRecruiter()->getEmail(),
                        'AdventureBundle:Emails:potential/accepted_potential_helpdesk.html.twig'
                    );
                    $this->sendEmail(
                        $person,
                        $subject,
                        $this->personnelEmail,
                        $employee->getRecruiter()->getEmail(),
                        'AdventureBundle:Emails:potential/accepted_potential_personnel.html.twig'
                    );
                    $tribeId = $employee->getDesignatedTribe()->getId();
                    /** @var Tribe $tribe */
                    $tribe = $this->entityManager->getRepository(Tribe::class)->find($tribeId);
                    $tribeHREmail = $tribe->getHrEmail();
                    if ($tribeHREmail !== '') {
                        $this->sendEmail(
                            $person,
                            $subject,
                            $tribeHREmail,
                            $employee->getRecruiter()->getEmail(),
                            'AdventureBundle:Emails:potential/accepted_potential_tribe.html.twig'
                        );
                    } else {
                        $this->sendEmail(
                            $person,
                            $subject,
                            $employee->getRecruiter()->getEmail(),
                            null,
                            'AdventureBundle:Emails:potential/tribe_email_not_set.html.twig'
                        );
                    }
                    $this->sendEmail(
                        $person,
                        $subject,
                        $this->receptionEmail,
                        $employee->getRecruiter()->getEmail(),
                        'AdventureBundle:Emails:potential/accepted_potential_reception.html.twig'
                    );
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(
                "Doctrine error while updating potential employee: ".$e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function sendEmail($person, $subject, $email, $cc, $template)
    {
        $this->mailer->sendMessage($email, $cc, $subject, ['person' => $person], $template);
    }

    /**
     * @param int $id
     * @return PotentialEmployee
     * @throws \Exception
     */
    private function getEmployee(int $id) : PotentialEmployee
    {
        $repo = $this->entityManager->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee|null $employee */
        $employee = $repo->find($id);
        if (is_null($employee)) {
            throw new \Exception(
                "Potential employee with ID $id has not been found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $employee;
        }
    }

    /**
     * @param UpdatePotentialEmployee $message
     * @return Employee|null
     * @throws \Exception
     */
    private function getJoinedEmployee(UpdatePotentialEmployee $message) : ?Employee
    {
        $id = $message->getJoinedEmployeeId();
        if (is_null($id)) {
            return null;
        }
        $repo = $this->entityManager->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($id);
        if (is_null($employee)) {
            throw new \Exception("Employee with ID $id has not been found", Response::HTTP_NOT_FOUND);
        } else {
            return $employee;
        }
    }
}
