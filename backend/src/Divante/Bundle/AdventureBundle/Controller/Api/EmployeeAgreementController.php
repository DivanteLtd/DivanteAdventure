<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\User;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateEmployeeAgreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteEmployeeAgreement;
use Divante\Bundle\AdventureBundle\Query\Pairings\PairingsQuery;
use Divante\Bundle\AdventureBundle\Query\Agreement\GDPRAcceptationQuery;
use Divante\Bundle\AdventureBundle\Query\Agreement\MarketingAcceptationQuery;
use Divante\Bundle\AdventureBundle\Query\Agreement\ISOAcceptationQuery;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Employeeagreement controller.
 *
 * @Route("api/employeeagreement")
 */
class EmployeeAgreementController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var PairingsQuery */
    private $query;
    /** @var GDPRAcceptationQuery */
    private $GDPRAcceptationQuery;
    /** @var MarketingAcceptationQuery */
    private $marketingAcceptationQuery;

    public function __construct(
        MessageBusInterface $messageBus,
        PairingsQuery $query,
        GDPRAcceptationQuery $GDPRAcceptationQuery,
        MarketingAcceptationQuery $marketingAcceptationQuery
    ) {
        $this->messageBus = $messageBus;
        $this->query = $query;
        $this->GDPRAcceptationQuery = $GDPRAcceptationQuery;
        $this->marketingAcceptationQuery = $marketingAcceptationQuery;
    }

    /**
     * Creates a new employee agreements.
     *
     * @Route("/{id}", name="employeeagreement_new")
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function newAction(int $id, Request $request): View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employeeId = $user->getEmployeeId();
        if ($employeeId !== $id) {
            return $this->view([], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $data = json_decode($request->getContent(), true);
            $em = $this->getDoctrine()->getManager();
            $agreement = $em->getRepository(Agreement::class)->find($data[0]);
            if ($agreement->getType() === 1) {
                $createEntry = new DeleteEmployeeAgreement($id);
                $this->messageBus->dispatch($createEntry);
            }
            foreach ($data as $entry) {
                $createEntry = new CreateEmployeeAgreement($id, $entry);
                $this->messageBus->dispatch($createEntry);
            }
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List GDPR acceptation list.
     *
     * @Route("/gdpr", name="employeeagreement_gdpr")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     *
     * @return View
     */
    public function getGDPRAcceptationList(): View
    {
        try {
            $acceptationList = $this->GDPRAcceptationQuery->get();
            return $this->view($acceptationList, Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List marketing acceptation list.
     *
     * @Route("/marketing", name="employeeagreement_marketing")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     *
     * @return View
     */
    public function getMarketingAcceptationList(): View
    {
        try {
            $acceptationList = $this->marketingAcceptationQuery->get();
            return $this->view($acceptationList, Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List iso acceptation list.
     *
     * @Route("/iso", name="employeeagreement_iso")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @param ISOAcceptationQuery $iSOAcceptationQuery
     * @return View
     */
    public function getISOAcceptationList(ISOAcceptationQuery $iSOAcceptationQuery): View
    {
        try {
            $acceptationList = $iSOAcceptationQuery->get();
            return $this->view($acceptationList, Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
