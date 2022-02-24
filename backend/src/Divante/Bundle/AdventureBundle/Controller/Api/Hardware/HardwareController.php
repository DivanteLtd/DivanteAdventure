<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Entity\Repository\Hardware\HardwareAgreementRepository;
use Divante\Bundle\AdventureBundle\Message\Hardware\DeleteHardwareAgreementEntry;
use Divante\Bundle\AdventureBundle\Message\Hardware\GenerateHardwareAgreement;
use Divante\Bundle\AdventureBundle\Message\Hardware\SignHardwareAgreement;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Services\Hardware\PasswordGenerator;
use Divante\Bundle\AdventureBundle\Entity\User;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Divante\Bundle\AdventureBundle\Mappers\Hardware\HardwareGenerateMapper;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Hardware controller.
 *
 * @Route("api/hardware/")
 */
class HardwareController extends FOSRestController
{
    /**
     * Get data to generate hardware agreements
     * @Route("agreement", name="get_hardware_agreements")
     * @Method("GET")
     * @param HardwareGenerateMapper $mapper
     * @return View
     */
    public function getHardwareGenerateAgreements(HardwareGenerateMapper $mapper) : View
    {
        /** @var HardwareAgreementRepository $hardwareRepo */
        $hardwareRepo = $this->getDoctrine()->getRepository(HardwareAgreement::class);
        /** @var HardwareAgreement[] $hardwareAgreements */
        $hardwareAgreements = $hardwareRepo->getUnsignedAgreements();
        try {
            $json = array_map(fn(HardwareAgreement $a) => $mapper->mapEntity($a), $hardwareAgreements);
            return $this->view($json, Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get data to generate hardware agreements
     * @Route("agreement/to_sign", name="get_hardware_agreements_to_sign")
     * @Method("GET")
     * @param HardwareGenerateMapper $mapper
     * @param EmailConfig $config
     * @return View
     */
    public function getAgreementsToSign(HardwareGenerateMapper $mapper, EmailConfig $config) : View
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $employee = $user->getEmployee();
            /** @var HardwareAgreementRepository $hardwareRepo */
            $hardwareRepo = $this->getDoctrine()->getRepository(HardwareAgreement::class);
            $hardwareAgreements = $hardwareRepo->getAgreementsForUser($employee, $config);
            $json = array_map(fn(HardwareAgreement $a) => $mapper->mapEntity($a), $hardwareAgreements);
            return $this->view($json, Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Save form with data to agreement
     * @Route("agreement", name="save_hardware_agreement")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @param PasswordGenerator $generator
     * @return View
     */
    public function saveAgreement(Request $request, MessageBusInterface $messageBus, PasswordGenerator $generator) :View
    {
        try {
            $password = $generator->generatePassword();
            $message = new GenerateHardwareAgreement(
                $request->get('id'),
                $password,
                $request->get('name'),
                $request->get('lastName'),
                $request->get('manufacturer'),
                $request->get('model'),
                $request->get('serialNumber'),
                $request->get('PESEL'),
                $request->get('NIP'),
                $request->get('company'),
                $request->get('headquarters'),
                $request->get('languages')
            );
            $messageBus->dispatch($message);
            return $this->view(['password' => $password], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("{id}", name="delete_hardware_agreement_entry")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteHardwareAgreementEntry(MessageBusInterface $messageBus, int $id): View
    {
        $message = new DeleteHardwareAgreementEntry($id);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("{id}/sign", name="hardware_agreement_sign_by_id")
     * @Method("POST")
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function signAgreement(int $id, Request $request, MessageBusInterface $messageBus) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        /** @var string $password */
        $password = $request->get('password', '');
        $message = new SignHardwareAgreement($id, $password, $employee);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
