<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Message\Employee\CreatePotentialEmployee;
use Symfony\Component\HttpFoundation\Response;

class CreatePotentialEmployeeHandler extends AbstractPotentialEmployeeHandler
{
    /**
     * @param CreatePotentialEmployee $message
     * @throws \Exception
     */
    public function __invoke(CreatePotentialEmployee $message) : void
    {
        $name = $message->getName();
        $lastName = $message->getLastName();
        $hireDate = $this->getHireDate($message);
        $tribe = $this->getTribe($message);
        $position = $this->getPosition($message);
        $email = $message->getEmail();
        $contractType = $message->getContractType();
        $gender = $message->getGender();
        $privatePhone = $message->getPrivatePhone();
        $city = $message->getCity();
        $postalCode = $message->getPostalCode();
        $street = $message->getStreet();
        $country = $message->getCountry();
        $dateOfBirth = $this->getDateOfBirthDate($message);
        $privateEmail = $message->getPrivateEmail();
        $recruiterId = $message->getRecruiterId();
        $source = $message->getSource();
        $company = $message->getCompany();
        $welcomeDay = $message->getWelcomeDay();

        $recruiter = $this->entityManager->getRepository(Employee::class)
            ->find($recruiterId);

        $employee = new PotentialEmployee();
        $employee
            ->setName($name)
            ->setLastName($lastName)
            ->setDesignatedHireDate($hireDate)
            ->setDesignatedTribe($tribe)
            ->setDesignatedPosition($position)
            ->setEmail($email)
            ->setContractType($contractType)
            ->setStatus(PotentialEmployee::STATUS_POTENTIAL_EMPLOYEE)
            ->setGender($gender)
            ->setDateOfBirth($dateOfBirth)
            ->setPrivatePhone($privatePhone)
            ->setCity($city)
            ->setPostalCode($postalCode)
            ->setStreet($street)
            ->setCountry($country)
            ->setWelcomeDayDate($welcomeDay === '' ? null : new \DateTimeImmutable($welcomeDay))
            ->setPrivateEmail($privateEmail)
            ->setLanguage($message->getLanguage() ?? 'en')
            ->setRecruiter($recruiter)
            ->setSource($source)
            ->setCompany($company)
            ->setOutsourceSubType($message->getOutsourceSubType())
            ->setCreatedAt()
            ->setUpdatedAt();
        try {
            $this->entityManager->persist($employee);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception(
                "Doctrine error while creating new potential employee: ".$e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
