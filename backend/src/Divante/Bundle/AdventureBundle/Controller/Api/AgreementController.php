<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal\DbalAgreementQuery;
use Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal\DbalMarketingConsentsQuery;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateAgreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteAgreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\UpdateAgreement;
use Symfony\Component\Messenger\MessageBusInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Divante\Bundle\AdventureBundle\Entity\Agreement;

/**
 * Agreement controller.
 *
 * @Route("api/agreement")
 */
class AgreementController extends FOSRestController
{

    /** @var MessageBusInterface */
    private $messageBus;
    /** @var DbalAgreementQuery */
    private $query;
    /** @var DbalMarketingConsentsQuery */
    private $marketingQuery;

    public function __construct(
        MessageBusInterface $messageBus,
        DbalAgreementQuery $query,
        DbalMarketingConsentsQuery $marketingQuery
    ) {
        $this->messageBus = $messageBus;
        $this->query = $query;
        $this->marketingQuery = $marketingQuery;
    }


    /**
     * Lists all agreement entities.
     *
     * @Route("/{id}", name="agreement_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param int $id
     * @return View
     */
    public function indexAction(int $id)
    {
        $agreement = $this->query->getAll($id);
        return $this->view($agreement, Response::HTTP_OK);
    }

    /**
     * Lists all marketing consents.
     *
     * @Route("/marketing/{id}", name="agreement_marketing")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param int $id
     * @return View
     */
    public function getMarketingConsents(int $id): View
    {
        $consents = $this->marketingQuery->getAll($id);
        return $this->view($consents, Response::HTTP_OK);
    }

    /**
     * Creates a new agreement entity.
     *
     * Access: ADMIN
     *
     * @Route("", name="agreement_new")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param Request $request
     * @return View
     */
    public function newAction(Request $request)
    {
        try {
            $namePl = $request->get('namePl', '');
            $nameEn = $request->get('nameEn', '');
            $descriptionPl = $request->get('descriptionPl', '');
            $descriptionEn = $request->get('descriptionEn', '');
            $required = (bool)$request->get('isRequired', false);
            $priority = $request->get('displayOrder', -1);
            $contracts = $request->get('contracts', []);
            $attachments = $request->get('attachments', []);
            $type = $request->get('type', 0);
            $createEntry = new CreateAgreement(
                $namePl,
                $nameEn,
                $descriptionPl,
                $descriptionEn,
                $required,
                $priority,
                $contracts,
                $attachments,
                $type
            );
            $this->messageBus->dispatch($createEntry);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Edit an existing agreement entity.
     *
     * Access: ADMIN
     *
     * @Route("/{id}", name="agreement_edit")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param int $id
     * @param Request $request
     *
     * @return View
     */
    public function editAction(int $id, Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Agreement|null $entry */
            $entry = $em->getRepository(Agreement::class)->find($id);
            if (is_null($entry)) {
                return $this->view(['error' => "Agreement with ID $id not found"], Response::HTTP_NOT_FOUND);
            }
            $namePl = $request->get('namePl', '');
            $nameEn = $request->get('nameEn', '');
            $descriptionPl = $request->get('descriptionPl', '');
            $descriptionEn = $request->get('descriptionEn', '');
            $required = (bool)$request->get('isRequired', false);
            $priority = $request->get('displayOrder', -1);
            $contracts = $request->get('contracts', []);
            $attachments = $request->get('attachments', []);
            $type = $request->get('type', 0);
            $createEntry = new UpdateAgreement(
                $entry,
                $namePl,
                $nameEn,
                $descriptionPl,
                $descriptionEn,
                $required,
                $priority,
                $contracts,
                $attachments,
                $type
            );
            $this->messageBus->dispatch($createEntry);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete agreement
     *
     * @Route("/{id}", name="agreement_delete")
     * @Method("DELETE")
     *
     * Access: ADMIN
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param int $id
     *
     * @return View
     */
    public function deleteAction(int $id): View
    {
        $message = new DeleteAgreement($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
