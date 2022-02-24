<?php

namespace Divante\Bundle\AdventureBundle\Representation\Factory;

use Pagerfanta\Pagerfanta;

class PagerfantaFactory
{
    /** @var Pagerfanta<mixed> */
    private Pagerfanta $pager;

    /**
     * PagerfantaFactory constructor.
     * @param Pagerfanta<mixed> $pager
     */
    public function __construct(Pagerfanta $pager)
    {
        $this->pager = $pager;
    }

    /** @return array<string,mixed> */
    public function createRepresentation() : array
    {
        return [
            'totalResults' => $this->pager->getNbResults(),
            'currentPage' => $this->pager->getCurrentPage(),
            'totalPages' => $this->pager->getNbPages(),
            'maxPerPage' => $this->pager->getMaxPerPage(),
            'offsetStart' => $this->pager->getCurrentPageOffsetStart(),
            'offsetEnd' => $this->pager->getCurrentPageOffsetEnd(),
            'haveToPaginate' => $this->pager->haveToPaginate(),
            'previousPage' => $this->pager->hasPreviousPage() ? $this->pager->getPreviousPage() : null,
            'nextPage' => $this->pager->hasNextPage() ? $this->pager->getNextPage() : null,
        ];
    }
}
