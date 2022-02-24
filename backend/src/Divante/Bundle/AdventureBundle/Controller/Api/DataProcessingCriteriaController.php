<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Mappers\CriteriaMapper;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateCriterion;
use Divante\Bundle\AdventureBundle\Message\Agreement\UpdateCriterion;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteCriterion;
use Exception;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @Route("api/criteria")
 */
final class DataProcessingCriteriaController extends FOSRestController
{
    private CriteriaMapper $criteriaMapper;
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus, CriteriaMapper $criteriaMapper)
    {
        $this->criteriaMapper = $criteriaMapper;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("", name="criteria_list")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function listAction() : View
    {
        return $this->view($this->getCriteria(), Response::HTTP_OK);
    }

    /**
     * @Route("", name="criteria_create")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @return View
     */
    public function createCriteriaAction(Request $request) : View
    {
        try {
            $namePl = $request->get('namePl');
            $nameEn = $request->get('nameEn');
            if (empty($namePl) || empty($nameEn)) {
                throw new BadRequestHttpException("Lacking parameter 'namePl' or 'nameEn'");
            }
            $message = new CreateCriterion($namePl, $nameEn);
            $this->messageBus->dispatch($message);
            return $this->view($this->getCriteria(), Response::HTTP_OK);
        } catch (BadRequestHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="criteria_update")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateCriteria(Request $request, int $id) : View
    {
        try {
            $namePl = $request->get('namePl');
            $nameEn = $request->get('nameEn');
            if (empty($namePl) || empty($nameEn)) {
                throw new BadRequestHttpException("Lacking parameter 'namePl' or 'nameEn'");
            }
            $message = new UpdateCriterion($namePl, $nameEn, $id);
            $this->messageBus->dispatch($message);
            return $this->view($this->getCriteria(), Response::HTTP_OK);
        } catch (BadRequestHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }/**
     * @Route("/{id}", name="criteria_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $id
     * @return View
     */
    public function deleteCriteria(int $id) : View
    {
        $message = new DeleteCriterion($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    protected function getCriteria() : array
    {
        $repo = $this->getDoctrine()->getRepository(DataProcessingCriteria::class);
        $criteria = $repo->findAll();
        return array_map($this->criteriaMapper, $criteria);
    }
}
