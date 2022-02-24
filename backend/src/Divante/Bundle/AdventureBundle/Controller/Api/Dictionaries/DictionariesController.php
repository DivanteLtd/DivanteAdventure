<?php


namespace Divante\Bundle\AdventureBundle\Controller\Api\Dictionaries;

use Divante\Bundle\AdventureBundle\Entity\ContractType;
use Divante\Bundle\AdventureBundle\Mappers\FAQ\FAQCategoryDetailsMapper;
use Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract\CreateContractType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Dictionaries controller.
 *
 * @Route("api/dictionaries/")
 */
class DictionariesController extends FOSRestController
{

    /**
     * @Route("contracts", name="get_contracts_type")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function getContractsType() :View
    {
        $contractsType = $this->getDoctrine()->getRepository(ContractType::class)->findAll();
        return $this->view($contractsType, Response::HTTP_OK);
    }
    /**
     * @Route("contract", name="new_contract_typr")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addContractType(Request $request, MessageBusInterface $messageBus) :View
    {
        $message = new CreateContractType(
            $request->get('code'),
            $request->get('name'),
            $request->get('description'),
            $request->get('active')
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
