<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 27.03.19
 * Time: 10:39
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Events\Tribe\TribeAssignmentChangeEvent;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateEmployeeHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UpdateEmployee $message
     * @throws \Exception
     */
    public function __invoke(UpdateEmployee $message) : void
    {
        $event = null;
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->em->getRepository(Employee::class);
        /** @var Employee $employee */
        $employee = $employeeRepo->find($message->getId());
        $user = $employee->getUser();
        if ($message->getFirstName() !== null) {
            $employee->setName($message->getFirstName());
        }
        if ($message->getLastName() !== null) {
            $employee->setLastName($message->getLastName());
        }
        if ($message->getEmail() !== null) {
            $employee->setEmail($message->getEmail());
        }
        if ($message->getPhotoUrl() !== null) {
            $employee->setPhoto($message->getPhotoUrl());
        }
        if ($message->getPrivatePhone() !== null) {
            $employee->setPrivatePhone($message->getPrivatePhone());
        }
        if ($message->getCompanyPhone() !== null) {
            $employee->setPhone($message->getCompanyPhone());
        }
        if ($message->getCity() !== null) {
            $employee->setCity($message->getCity());
        }
        if ($message->getPostalCode() !== null) {
            $employee->setPostalCode($message->getPostalCode());
        }
        if ($message->getStreet() !== null) {
            $employee->setStreet($message->getStreet());
        }
        if ($message->getCountry() !== null) {
            $employee->setCountry($message->getCountry());
        }
        if ($message->getContractId() !== null) {
            $employee->setContractId($message->getContractId());
        }
        if ($message->getHiredAt() !== null) {
            $employee->setHiredAt($message->getHiredAt());
        }
        if ($message->getLicencePlate() !== null) {
            $employee->setCar($message->getLicencePlate());
        }
        if ($message->getWorkMode() !== null) {
            $employee->setWorkMode((int)$message->getWorkMode());
        }
        if ($message->getTribeId() !== null) {
            $oldTribeId = is_null($employee->getTribe()) ? null : $employee->getTribe()->getId();
            if ($oldTribeId !== $message->getTribeId()) {
                $event = new TribeAssignmentChangeEvent($employee->getId(), $oldTribeId, $message->getTribeId());
                $employee
                    ->setTribe($this->getTribeById($message->getTribeId()))
                    ->setPosition(null)
                    ->setLevel(null);
            }
        }
        if (!is_null($message->getPositionId())) {
            $employee
                ->setPosition($this->getPositionById($message->getPositionId()))
                ->setLevel(null);
        }
        if (!is_null($message->getLevelId())) {
            $employee->setLevel($this->getLevelById($message->getLevelId()));
        }
        if ($message->getGender() !== null) {
            $employee->setGender($message->getGender());
        }
        if ($message->getEmergencyFirstName() !== null) {
            $employee->setEmergencyFirstName($message->getEmergencyFirstName());
        }
        if ($message->getEmergencyLastName() !== null) {
            $employee->setEmergencyLastName($message->getEmergencyLastName());
        }
        if ($message->getEmergencyAddress() !== null) {
            $employee->setEmergencyAddress($message->getEmergencyAddress());
        }
        if ($message->getEmergencyPhone() !== null) {
            $employee->setEmergencyPhone($message->getEmergencyPhone());
        }

        if ($message->getSubTypeContract() !== null) {
            $employee->setOutsourceSubType($message->getSubTypeContract());
        }
        if ($message->getRoles() !== null && !is_null($user)) {
            $isSuperAdmin = !is_null($message->getCallingEmployee())
                && !is_null($message->getCallingEmployee()->getUser())
                && !empty($message->getCallingEmployee()->getUser()->getRoles())
                && in_array('ROLE_SUPER_ADMIN', $message->getCallingEmployee()->getUser()->getRoles());
            $isDemoEnabled = (bool)($_ENV['DEMO_ENABLED'] ?? false);
            if ($isSuperAdmin || $isDemoEnabled) {
                $user->setRoles($message->getRoles());
            }
        }
        if (!is_null($message->getJobTimeValue())) {
            $employee->setJobTimeValue($message->getJobTimeValue());
        }
        if (!is_null($message->getPin())) {
            if ($employee->getHashedPin() === null) {
                $employee->setAndHashPin($message->getPin());
            } else {
                if ($employee->validatePin($message->getOldPin())) {
                    $employee->setAndHashPin($message->getPin());
                } else {
                    throw new \Exception("Old password is incorrect.", 0);
                }
            }
        }
        if (!is_null($message->getDateOfBirth())) {
            $employee->setDateOfBirth($message->getDateOfBirth());
        }
        if (!is_null($message->getChildCare())) {
            $employee->setChildCare($message->getChildCare());
        }
        if (!is_null($message->getLanguage())) {
            $employee->setLanguage($message->getLanguage());
        }
        if (!is_null($message->getNip())) {
            $employee->setNip($message->getNip());
        }
        if (!is_null($message->getFirmName())) {
            $employee->setFirmName($message->getFirmName());
        }
        if (!is_null($message->getFirmAddress())) {
            $employee->setFirmAddress($message->getFirmAddress());
        }
        if (!is_null($message->getDataUpdate())) {
            $employee->setDataUpdateTime($message->getDataUpdate());
        }
        if (!is_null($message->getEmployeeCode())) {
            if (mb_strlen($message->getEmployeeCode()) > 100) {
                throw new \Exception('The code is longer than 100.', 0);
            }
            if (count($employeeRepo->getByEmployeeCode($message->getEmployeeCode())) != 0) {
                throw new \Exception('The code is not unique', 0);
            }
            $employee->setEmployeeCode($message->getEmployeeCode());
        }

        if (!is_null($message->isStudent())) {
            $employee->setStudent($message->isStudent());
        }
        if (!is_null($message->getTaxDeductibleCosts())) {
            $employee->setTaxDeductibleCosts($message->getTaxDeductibleCosts());
        }
        if (!is_null($message->getWorkCountry())) {
            $employee->setWorkCountry($message->getWorkCountry());
        }
        if (!is_null($message->getWorkPostalCode())) {
            $employee->setWorkPostalCode($message->getWorkPostalCode());
        }
        if (!is_null($message->getWorkCity())) {
            $employee->setWorkCity($message->getWorkCity());
        }
        if (!is_null($message->getWorkStreet())) {
            $employee->setWorkStreet($message->getWorkStreet());
        }
        if (!is_null($message->getFinanceCode())) {
            $code = $message->getFinanceCode();
            $employee->setFinanceCode($code);
        }
        if (!is_null($message->getSuperiorEmail())) {
            $supervisor = $this->getSuperiorByEmail($message->getSuperiorEmail());
            $employee->setSuperior($supervisor);
        }
        if (!is_null($message->getShoeSize())) {
            $employee->setShoeSize($message->getShoeSize());
        }
        if (!is_null($message->getSweatshirtSize())) {
            $employee->setSweatshirtSize($message->getSweatshirtSize());
        }
        if (!is_null($message->getShirtSize())) {
            $employee->setShirtSize($message->getShirtSize());
        }
        $this->em->getConnection()->beginTransaction();
        try {
            $employee->setUpdatedAt();
            $this->em->persist($employee);
            if (!is_null($user)) {
                $this->em->persist($user);
            }
            $this->em->flush();
            $this->em->getConnection()->commit();
            if (!is_null($event)) {
                $this->dispatcher->dispatch($event);
            }
        } catch (\Exception $exception) {
            $this->em->getConnection()->rollBack();
            throw new \Exception("Updating employee entry failed", 0, $exception);
        }
    }

    private function getTribeById(int $id) : ?Tribe
    {
        $repo = $this->em->getRepository(Tribe::class);
        /** @var Tribe|null $tribe */
        $tribe = $repo->find($id);
        return $tribe;
    }

    private function getPositionById(int $id) : ?Position
    {
        $repo = $this->em->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repo->find($id);
        return $position;
    }

    private function getLevelById(int $id) : ?Level
    {
        $repo = $this->em->getRepository(Level::class);
        /** @var Level|null $level */
        $level = $repo->find($id);
        return $level;
    }

    private function getSuperiorByEmail(string $email) :Employee
    {
        /** @var EmployeeRepository $repo */
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->findOneByEmailAddressUsername($email);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with email: $email has not been found");
        }
        return $employee;
    }
}
