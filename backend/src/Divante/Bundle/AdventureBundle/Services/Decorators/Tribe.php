<?php


namespace Divante\Bundle\AdventureBundle\Services\Decorators;

use Divante\Bundle\AdventureBundle\Entity\Tribe as TribeRepo;

class Tribe extends AbstractDecorator
{

    public function decorate(?int $id): string
    {
        if (is_null($id)) {
            return '';
        }
        $tribeRepo = $this->getEntityManager()->getRepository(TribeRepo::class);
        $tribe = $tribeRepo->find($id);
        return $tribe->getName();
    }
}
