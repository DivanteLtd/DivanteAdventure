<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.12.18
 * Time: 11:28
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Data\NamedEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\SchedulerHideable;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FiltersController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/filters")
 */
class FiltersController extends FOSRestController
{
    /** @var array<string,string> */
    private const FILTERS = [
        'tribe' => 'AdventureBundle:Tribe',
        'position' => 'AdventureBundle:Position',
    ];

    /**
     * @Route("", name="filters_index")
     * @Security("is_granted('FILTER_LIST')")
     * @Method("GET")
     * @return View
     */
    public function indexAction() : View
    {
        $cachedFilters = $this->get('cache.app')->getItem('filters');
        if (!$cachedFilters->isHit()) {
            $resultArrays = [];
            $em = $this->getDoctrine()->getManager();
            /**
             * @var string $name
             * @var string $repositoryName
             */
            foreach (self::FILTERS as $name => $repositoryName) {
                $repository = $em->getRepository($repositoryName);
                /** @var NamedEntity[] $entities */
                $entities = array_filter(
                    $repository->findAll(),
                    function ($entity) {
                        return !($entity instanceof SchedulerHideable && $entity->isHiddenFromScheduler());
                    }
                );
                $resultArrays[] = $this->mapEntities($entities, $name);
            }
            $result = array_merge(...$resultArrays);
            $cachedFilters->set($result);
            $this->get('cache.app')->save($cachedFilters);
        } else {
            $result = $cachedFilters->get();
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @param NamedEntity[] $entities
     * @param string $name
     * @return array<int,array<string,string>>
     */
    private function mapEntities(array $entities, string $name) : array
    {
        return array_map(
            function ($entity) use ($name) {
                return $this->mapNamedEntity($entity, $name);
            },
            $entities
        );
    }

    /**
     * @param NamedEntity $entity
     * @param string $name
     * @return array<string,string>
     */
    private function mapNamedEntity(NamedEntity $entity, string $name) : array
    {
        return [
            "id" => $name.'/'.$entity->getId(),
            "label" => $entity->getName()
        ];
    }
}
