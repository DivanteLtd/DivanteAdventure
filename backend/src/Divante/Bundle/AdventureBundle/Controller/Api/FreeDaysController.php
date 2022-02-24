<?php
namespace Divante\Bundle\AdventureBundle\Controller\Api;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\PublicHoliday;
use Divante\Bundle\AdventureBundle\Mappers\PublicHolidayMapper;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller returning free days.
 * @Route("api/freeDays")
 * @package Divante\Bundle\AdventureBundle\Controller
 */
class FreeDaysController extends FOSRestController
{

    /**
     * List all free days.
     * @Route("", name="freedays_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Free days",
     *     resource=true,
     *     description="Gets all free days",
     *     output="array of strings",
     *     statusCodes={
     *         200="Returned Free days list when successful",
     *         400="Bad request",
     *         403="Forbidden for this user",
     *         404="Free days not found",
     *         405="Method not allowed",
     *     }
     * )
     * @param Request $request
     * @param FreeDaysSupplier $supplier
     * @return View
     */
    public function indexAction(Request $request, FreeDaysSupplier $supplier) : View
    {
        $yearGte = (int)$request->query->get('year_gte', date("Y", strtotime('-2 years')));
        $yearLte = (int)$request->query->get('year_lte', date("Y", strtotime('+2 years')));
        $freeDays = $supplier->getFreeDays($yearGte, $yearLte);
        return $this->view($freeDays, Response::HTTP_OK);
    }

    /**
     * @Route("/entries")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param PublicHolidayMapper $mapper
     * @return View
     */
    public function getEntriesAction(PublicHolidayMapper $mapper): View
    {
        $repo = $this->getDoctrine()->getRepository(PublicHoliday::class);
        /** @var PublicHoliday[] $entries */
        $entries = $repo->findAll();
        $result = array_map($mapper, $entries);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/entries/{id}/switch", requirements={"id"="\d+"})
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @return View
     */
    public function switchEntryAction(int $id): View
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(PublicHoliday::class);
        /** @var PublicHoliday|null $entry */
        $entry = $repo->find($id);
        if (is_null($entry)) {
            return $this->view(['error' => "Entry with ID $id not found"], Response::HTTP_NOT_FOUND);
        }
        $entry->setEnabled(!$entry->isEnabled());
        $em->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/entries/{id}", requirements={"id"="\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @return View
     */
    public function deleteEntryAction(int $id): View
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(PublicHoliday::class);
        /** @var PublicHoliday|null $entry */
        $entry = $repo->find($id);
        if (is_null($entry)) {
            return $this->view(['error' => "Entry with ID $id not found"], Response::HTTP_NOT_FOUND);
        }
        if (!is_null($entry->getCalculationType())) {
            return $this->view(
                ['error' => "Entry with ID $id is calculated and cannot be deleted"],
                Response::HTTP_BAD_REQUEST
            );
        }
        $em->remove($entry);
        $em->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/entries")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function createEntryAction(Request $request): View
    {
        $date = (string)$request->get('date');
        $repeating = (bool)$request->get('repeating', false);
        $name = (string)$request->get('name', "");
        $entry = new PublicHoliday();
        $entry
            ->setDate(new DateTime($date))
            ->setEnabled(true)
            ->setName($name)
            ->setCalculationType(null)
            ->setRepeating($repeating)
            ->setCreatedAt()
            ->setUpdatedAt();
        $em = $this->getDoctrine()->getManager();
        $em->persist($entry);
        $em->flush();
        return $this->view([], Response::HTTP_OK);
    }
}
