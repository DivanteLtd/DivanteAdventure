<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Mappers\PotentialEmployeeMapper;
use Divante\Bundle\AdventureBundle\Message\Employee\CreatePotentialEmployee;
use Divante\Bundle\AdventureBundle\Message\Employee\DeletePotentialEmployee;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdatePotentialEmployee;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Collator;

/**
 * Class PotentialEmployeeController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("/api/potential_employee")
 */
class PotentialEmployeeController extends FOSRestController
{
    private MessageBusInterface $messageBus;
    private PotentialEmployeeMapper $potentialEmployeeMapper;

    public function __construct(
        MessageBusInterface $messageBus,
        PotentialEmployeeMapper $potentialEmployeeMapper
    ) {
        $this->messageBus = $messageBus;
        $this->potentialEmployeeMapper = $potentialEmployeeMapper;
    }

    /**
     * @Route("", name="potential_employee_get")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @return View
     */
    public function getEmployees() : View
    {
        $repository = $this->getDoctrine()->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee[] $potentialEmployees */
        $potentialEmployees = $repository->findAll();
        $mappedJson = array_map($this->potentialEmployeeMapper, $potentialEmployees);
        $coll = new Collator('pl_PL');
        usort($mappedJson, function (array $a, array $b) use ($coll) {
            return $coll->compare($a['lastName'], $b['lastName']);
        });
        return $this->view($mappedJson, Response::HTTP_OK);
    }

    /**
     * @Route("", name="potential_employee_post")
     * @Method("POST")
     * @Security("has_role('ROLE_HR')")
     * @param Request $request
     * @return View
     */
    public function createNewEmployee(Request $request) : View
    {
        /** @var string|null $name */
        $name = $request->get('name');
        /** @var string|null $lastName */
        $lastName = $request->get('lastName');
        /** @var string|null $email */
        $email = $request->get('email');
        /** @var string|int|null $tribeId */
        $tribeId = $request->get('tribeId');
        /** @var string|int|null $positionId */
        $positionId = $request->get('positionId');
        /** @var string|null $hireDate */
        $hireDate = $request->get('hireDate');
        /** @var string|null $contractType */
        $contractType = $request->get('contractType');
        /** @var int|null $gender */
        $gender = $request->get('gender');
        /** @var string|null $dateOfBirth */
        $dateOfBirth = $request->get('dateOfBirth');
        /** @var string|null $privatePhone */
        $privatePhone = $request->get('privatePhone');
        /** @var string|null $city */
        $city = $request->get('city');
        /** @var string|null $postalCode */
        $postalCode = $request->get('postalCode');
        /** @var string|null $street */
        $street = $request->get('street');
        /** @var string|null $country */
        $country = $request->get('country');
        /** @var string|null $privateEmail */
        $privateEmail = $request->get('privateEmail');
        /** @var int|null $recruiterId */
        $recruiterId = $request->get('recruiterId');
        /** @var string|null $source */
        $source = $request->get('source');
        /** @var string|null $company */
        $company = $request->get('company');
        $nip = $request->get('nip');
        $firmName = $request->get('firmName');
        $firmAddress = $request->get('firmAddress');
        $welcomeDay = $request->get('welcomeDay');
        $langauge = $request->get('language', 'en');
        $outsourceSubType = $request->get('outsourceSubType');

        try {
            if (is_null($name)) {
                throw new Exception('Missing required parameter "name"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($lastName)) {
                throw new Exception('Missing required parameter "lastName"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($email)) {
                throw new Exception('Missing required parameter "email"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($contractType)) {
                throw new Exception('Missing required parameter "contractType"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($gender)) {
                throw new Exception('Missing required parameter "gender"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($dateOfBirth)) {
                throw new Exception('Missing required parameter "dateOfBirth"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($privatePhone)) {
                throw new Exception('Missing required parameter "privatePhone"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($privateEmail)) {
                throw new Exception('Missing required parameter "privateEmail"', Response::HTTP_BAD_REQUEST);
            }
            if (is_null($recruiterId)) {
                throw new Exception('Missing required parameter "recruiterId"', Response::HTTP_BAD_REQUEST);
            }
            $tribeId = is_null($tribeId) ? null : (int)$tribeId;
            $positionId = is_null($positionId) ? null : (int)$positionId;
            $city = is_null($city) ? null : (string)$city;
            $postalCode = is_null($postalCode) ? null : (string)$postalCode;
            $street = is_null($street) ? null : (string)$street;
            $country = is_null($country) ? null : (string)$country;
            $source = is_null($source) ? null : (string)$source;
            $company = is_null($company) ? null : (string)$company;
            $message = new CreatePotentialEmployee(
                trim($name),
                trim($lastName),
                trim($email),
                $tribeId,
                $positionId,
                $hireDate,
                $contractType,
                $gender,
                $dateOfBirth,
                $privatePhone,
                $city,
                $postalCode,
                $street,
                $country,
                $privateEmail,
                $recruiterId,
                $source,
                $company,
                $nip,
                $firmName,
                $firmAddress,
                $welcomeDay,
                $langauge,
                $outsourceSubType
            );
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="potential_employee_put", requirements={"id": "\d+"})
     * @Method("PATCH")
     * @Security("has_role('ROLE_HR')")
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateEmployee(Request $request, int $id) : View
    {
        /** @var string|null $name */
        $name = $request->get('name');
        /** @var string|null $lastName */
        $lastName = $request->get('lastName');
        /** @var string|null $email */
        $email = $request->get('email');
        /** @var string|int|null $tribeId */
        $tribeId = $request->get('tribeId');
        /** @var string|int|null $positionId */
        $positionId = $request->get('positionId');
        /** @var string|null $hireDate */
        $hireDate = $request->get('hireDate');
        /** @var string|int|null $status */
        $status = $request->get('status');
        /** @var string|null $rejectionCause */
        $rejectionCause = $request->get('rejectionCause');
        /** @var string|int|null $joinedEmployeeId */
        $joinedEmployeeId = $request->get('joinedEmployeeId');
        /** @var string|null $contractType */
        $contractType = $request->get('contractType');
        /** @var int|null $gender */
        $gender = $request->get('gender');
        /** @var string|null $dateOfBirth */
        $dateOfBirth = $request->get('dateOfBirth');
        /** @var string|null $privatePhone */
        $privatePhone = $request->get('privatePhone');
        /** @var string|null $city */
        $city = $request->get('city');
        /** @var string|null $postalCode */
        $postalCode = $request->get('postalCode');
        /** @var string|null $street */
        $street = $request->get('street');
        /** @var string|null $country */
        $country = $request->get('country');
        /** @var string|null $privateEmail */
        $privateEmail = $request->get('privateEmail');
        /** @var int|null $recruiterId */
        $recruiterId = $request->get('recruiter');
        /** @var string|null $source */
        $source = $request->get('source');
        /** @var string|null $company */
        $company = $request->get('company');
        $nip = $request->get('nip');
        $firmName = $request->get('firmName');
        $firmAddress = $request->get('firmAddress');
        $welcomeDay = $request->get('welcomeDay', null);
        $language = $request->get('language', 'en');

        $tribeId = is_null($tribeId) ? null : (int)$tribeId;
        $positionId = is_null($positionId) ? null : (int)$positionId;
        $status = is_null($status) ? null : (int)$status;
        $joinedEmployeeId = is_null($joinedEmployeeId) ? null : (int)$joinedEmployeeId;
        $email = is_null($email) ? null : trim((string)$email);
        $name = is_null($name) ? null : trim((string)$name);
        $lastName = is_null($lastName) ? null : trim((string)$lastName);
        $gender = is_null($gender) ? null : (int)$gender;
        $dateOfBirth = is_null($dateOfBirth) ? null : (string)$dateOfBirth;
        $contractType = is_null($contractType) ? null : (string)$contractType;
        $hireDate = is_null($hireDate) ? null : (string)$hireDate;
        $privatePhone = is_null($privatePhone) ? null : (string)$privatePhone;
        $city = is_null($city) ? null : (string)$city;
        $postalCode = is_null($postalCode) ? null : (string)$postalCode;
        $street = is_null($street) ? null : (string)$street;
        $country = is_null($country) ? null : (string)$country;
        $source = is_null($source) ? null : (string)$source;
        $company = is_null($company) ? null : (string)$company;

        try {
            $message = new UpdatePotentialEmployee(
                $id,
                $name,
                $lastName,
                $status,
                $email,
                $tribeId,
                $positionId,
                $hireDate,
                $rejectionCause,
                $joinedEmployeeId,
                $contractType,
                $gender,
                $dateOfBirth,
                $privatePhone,
                $city,
                $postalCode,
                $street,
                $country,
                $privateEmail,
                $recruiterId,
                $source,
                $company,
                $nip,
                $firmName,
                $firmAddress,
                $welcomeDay,
                $language,
            );
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{id}", name="potential_employee_delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_HR')")
     * @param int $id
     * @return View
     */
    public function deleteEmployee(int $id) : View
    {
        $message = new DeletePotentialEmployee($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
